-- Fecha o histórico cadastral de um dump da Receita. Roda pelo bin/pos-reimport.sh
-- depois de cada reimport bem-sucedido; idempotente (um dump = uma rodada, em
-- `historico_execucoes`).
--
-- 1) SAÍDA de sócios: o upsert do pipeline nunca apaga — sócio que saiu da
--    empresa ficava na base para sempre (QSA errado no grafo, na API e no
--    refresh da carteira, que por isso nunca alertava "sócio saiu"). Todo
--    sócio que o dump novo NÃO reescreveu (data_atualizacao anterior ao fim do
--    dump anterior) saiu: vira evento `socio_saida` e é apagado.
-- 2) ENTRADA de sócios: linha criada durante o dump novo, numa empresa que já
--    existia antes dele → evento `socio_entrada`.
-- 3) Carimba o dump (`referencia`) nos eventos que os gatilhos gravaram.
-- 4) Sanidade: um campo com mudanças demais num dump só (layout/normalização
--    do pipeline mudou, não a realidade) tem os eventos descartados; saídas de
--    sócios acima do limite abortam tudo (nada é apagado).
--
-- Corte: tudo que o dump anterior gravou tem data_atualizacao <= o último
-- processed_at dele; tudo que o dump novo gravou é posterior (o pipeline grava
-- data_atualizacao = CURRENT_TIMESTAMP em cada linha do upsert).
--
-- Variáveis (psql -v): max_saidas_pct (padrão 5), max_eventos_campo (padrão 5000000).

\set ON_ERROR_STOP on
\if :{?max_saidas_pct}
\else
    \set max_saidas_pct 5
\endif
\if :{?max_eventos_campo}
\else
    \set max_eventos_campo 5000000
\endif

SELECT set_config('vigilo.max_saidas_pct', :'max_saidas_pct', false) AS saidas,
       set_config('vigilo.max_eventos_campo', :'max_eventos_campo', false) AS eventos \gset pos_

DO $$
DECLARE
    dir_novo text;
    dir_ant text;
    corte timestamp;
    marco timestamptz;
    total_socios numeric;
    n_saidas bigint;
    n_entradas bigint := 0;
    abortar boolean;
    n_eventos bigint;
    obs text := '';
    campo_ruim record;
    max_saidas_pct numeric := current_setting('vigilo.max_saidas_pct')::numeric;
    max_eventos bigint := current_setting('vigilo.max_eventos_campo')::bigint;
BEGIN
    SELECT directory INTO dir_novo FROM processed_files GROUP BY directory ORDER BY directory DESC LIMIT 1;

    IF dir_novo IS NULL THEN
        RAISE NOTICE 'pos-reimport: processed_files vazia — nada a fazer';
        RETURN;
    END IF;

    IF EXISTS (SELECT 1 FROM historico_execucoes WHERE referencia = dir_novo) THEN
        RAISE NOTICE 'pos-reimport: dump % já processado', dir_novo;
        RETURN;
    END IF;

    SELECT directory INTO dir_ant FROM processed_files
        WHERE directory < dir_novo GROUP BY directory ORDER BY directory DESC LIMIT 1;

    IF dir_ant IS NULL THEN
        INSERT INTO historico_execucoes (referencia, status, observacao)
            VALUES (dir_novo, 'primeira_carga', 'primeiro dump da base — nada a comparar');
        RAISE NOTICE 'pos-reimport: % é a primeira carga — nada a comparar', dir_novo;
        RETURN;
    END IF;

    -- O dump novo precisa estar COMPLETO (o reimport pode ter parado no meio e
    -- seguir na semana seguinte): pelo menos tantos arquivos de sócios quanto o
    -- anterior. Incompleto = sai sem registrar (a próxima rodada tenta de novo).
    IF (SELECT count(*) FROM processed_files WHERE directory = dir_novo AND filename ILIKE 'socios%')
        < (SELECT count(*) FROM processed_files WHERE directory = dir_ant AND filename ILIKE 'socios%') THEN
        RAISE NOTICE 'pos-reimport: dump % ainda incompleto — tenta na próxima rodada', dir_novo;
        RETURN;
    END IF;

    SELECT max(processed_at) INTO corte FROM processed_files WHERE directory = dir_ant;
    -- "Quando" dos eventos de sócio: o início do processamento do dump novo.
    SELECT min(processed_at) INTO marco FROM processed_files WHERE directory = dir_novo;

    -- 1) Saídas
    CREATE TEMP TABLE pos_saidas ON COMMIT DROP AS
        SELECT socio_id, cnpj_basico, nome_socio, cnpj_cpf_do_socio, qualificacao_do_socio, data_entrada_sociedade
        FROM socios WHERE data_atualizacao <= corte;
    SELECT count(*) INTO n_saidas FROM pos_saidas;
    SELECT greatest(reltuples, 1) INTO total_socios FROM pg_class WHERE relname = 'socios';

    -- Saídas demais = dump/pipeline suspeito: NÃO mexe em sócio nenhum. Os passos
    -- 3 e 4 (carimbo + sanidade dos eventos de gatilho) rodam mesmo assim — eles
    -- não dependem das saídas, e um evento sem carimbo escaparia da sanidade.
    abortar := n_saidas > total_socios * max_saidas_pct / 100;

    IF NOT abortar THEN
        INSERT INTO historico_cadastral (cnpj_basico, campo, valor_anterior, valor_novo, detectado_em, referencia)
            SELECT cnpj_basico, 'socio_saida',
                   jsonb_build_object('nome', nome_socio, 'documento', cnpj_cpf_do_socio,
                                      'qualificacao', qualificacao_do_socio, 'entrada', data_entrada_sociedade)::text,
                   NULL, marco, dir_novo
            FROM pos_saidas;

        DELETE FROM socios s USING pos_saidas x WHERE s.socio_id = x.socio_id;

        -- 2) Entradas (só em empresa que já existia antes do dump novo)
        INSERT INTO historico_cadastral (cnpj_basico, campo, valor_anterior, valor_novo, detectado_em, referencia)
            SELECT s.cnpj_basico, 'socio_entrada', NULL,
                   jsonb_build_object('nome', s.nome_socio, 'documento', s.cnpj_cpf_do_socio,
                                      'qualificacao', s.qualificacao_do_socio, 'entrada', s.data_entrada_sociedade)::text,
                   marco, dir_novo
            FROM socios s
            JOIN empresas e ON e.cnpj_basico = s.cnpj_basico
            WHERE s.data_criacao > corte AND e.data_criacao <= corte;
        GET DIAGNOSTICS n_entradas = ROW_COUNT;
    END IF;

    -- 3) Carimba o dump nos eventos dos gatilhos
    UPDATE historico_cadastral SET referencia = dir_novo
        WHERE referencia IS NULL AND detectado_em > corte;

    -- 4) Sanidade por campo (só eventos de gatilho; sócio já passou pelo limite acima)
    FOR campo_ruim IN
        SELECT campo, count(*) AS n FROM historico_cadastral
        WHERE referencia = dir_novo AND campo NOT IN ('socio_saida', 'socio_entrada')
        GROUP BY campo HAVING count(*) > max_eventos
    LOOP
        DELETE FROM historico_cadastral WHERE referencia = dir_novo AND campo = campo_ruim.campo;
        obs := obs || format('%s: %s mudanças descartadas (acima de %s); ', campo_ruim.campo, campo_ruim.n, max_eventos);
        RAISE WARNING 'pos-reimport: campo % com % mudanças no dump % — descartadas', campo_ruim.campo, campo_ruim.n, dir_novo;
    END LOOP;

    SELECT count(*) INTO n_eventos FROM historico_cadastral WHERE referencia = dir_novo;

    IF abortar THEN
        INSERT INTO historico_execucoes (referencia, status, corte, socios_saida, eventos, observacao)
            VALUES (dir_novo, 'abortado', corte, n_saidas, n_eventos,
                    format('%s saídas de sócios (> %s%% da base) — nenhum sócio apagado; conferir o dump/pipeline e apagar esta linha para reprocessar. %s', n_saidas, max_saidas_pct, obs));
        RAISE WARNING 'pos-reimport: % saídas de sócios no dump % (acima de % por cento da base) — abortado, nenhum sócio apagado', n_saidas, dir_novo, max_saidas_pct;
        RETURN;
    END IF;

    INSERT INTO historico_execucoes (referencia, status, corte, socios_saida, socios_entrada, eventos, observacao)
        VALUES (dir_novo, 'ok', corte, n_saidas, n_entradas, n_eventos, nullif(obs, ''));

    RAISE NOTICE 'pos-reimport: dump % — % saídas e % entradas de sócios, % eventos no total', dir_novo, n_saidas, n_entradas, n_eventos;
END
$$;
