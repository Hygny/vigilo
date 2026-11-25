-- Monta a busca de PESSOAS numa tabela nova e troca no fim (a busca do app
-- segue no ar durante a montagem). Rodado pelo bin/pessoas-popular.sh e pelo
-- tests/deploy/cnpj-db-sql.test.sh (a MESMA conta). Depende de busca.sql
-- (busca_norm, pg_trgm) e de pessoas.sql (esqueleto).
--
-- Uma linha por pessoa provável = (nome aparado, documento): o CPF da Receita
-- vem mascarado (***NNNNNN**), então homônimos com os mesmos 6 dígitos do meio
-- ficam numa linha só (a tela avisa: vínculo provável).
-- chave = md5(documento || '|' || nome)::uuid — o App\Support\PessoaChave faz a
-- mesma conta em PHP.

DROP TABLE IF EXISTS busca_pessoa_nova;
CREATE TABLE busca_pessoa_nova AS
    SELECT md5(documento || '|' || nome_exibicao)::uuid AS chave,
           busca_norm(nome_exibicao) AS nome,
           nome_exibicao,
           documento,
           tipo,
           empresas
    FROM (
        SELECT btrim(nome_socio) AS nome_exibicao,
               coalesce(cnpj_cpf_do_socio, '') AS documento,
               min(identificador_de_socio) AS tipo,
               count(DISTINCT cnpj_basico)::int AS empresas
        FROM socios
        WHERE identificador_de_socio IN ('2', '3')
          AND nome_socio IS NOT NULL AND btrim(nome_socio) <> ''
        GROUP BY btrim(nome_socio), coalesce(cnpj_cpf_do_socio, '')
    ) p
    WHERE btrim(busca_norm(nome_exibicao)) <> '';
ALTER TABLE busca_pessoa_nova ADD PRIMARY KEY (chave);
-- GIN trigram: os termos soltos (LIKE '% TERMO%'). btree text_pattern_ops: nome
-- exato e "começa com o nome digitado" (o que sobe no ranking) em milissegundos.
CREATE INDEX idx_busca_pessoa_nova_gin ON busca_pessoa_nova USING gin (nome gin_trgm_ops);
CREATE INDEX idx_busca_pessoa_nova_nome ON busca_pessoa_nova (nome text_pattern_ops);
ANALYZE busca_pessoa_nova;

-- Posse do papel do app ANTES da troca: sem isso, entre o COMMIT e o
-- papel-app.sh a busca do app levaria "permission denied". (Num banco sem o
-- papel — o teste SQL descartável roda no mesmo servidor, então ele existe —
-- só pula.)
DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'vigilo_app') THEN
        ALTER TABLE busca_pessoa_nova OWNER TO vigilo_app;
    END IF;
END $$;

-- Troca atômica: o app passa a ver a tabela nova inteira de uma vez.
BEGIN;
DROP TABLE IF EXISTS busca_pessoa;
ALTER TABLE busca_pessoa_nova RENAME TO busca_pessoa;
ALTER TABLE busca_pessoa RENAME CONSTRAINT busca_pessoa_nova_pkey TO busca_pessoa_pkey;
ALTER INDEX idx_busca_pessoa_nova_gin RENAME TO idx_busca_pessoa_gin;
ALTER INDEX idx_busca_pessoa_nova_nome RENAME TO idx_busca_pessoa_nome;
COMMIT;
