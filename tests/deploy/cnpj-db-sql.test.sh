#!/usr/bin/env bash
#
# Testes dos objetos SQL que o Vigilo mantém dentro da base CNPJ
# (deploy/cnpj-db/sql: histórico cadastral, pos-reimport e busca por nome),
# num Postgres DE VERDADE — gatilhos, unaccent e pg_trgm não existem no SQLite.
#
# Cria um banco descartável (vigilo_sql_teste) no servidor apontado por PSQL,
# monta o schema do pipeline da Receita (fixtures/pipeline-initial.sql), simula
# dois dumps (upsert igual ao do pipeline) e confere o que ficou gravado.
#
# Rodar na VM da base (usa o container do Postgres):
#   bash tests/deploy/cnpj-db-sql.test.sh
# Ou contra outro servidor: PSQL="psql -h localhost -U postgres" bash ...
set -uo pipefail

HERE="$(cd "$(dirname "$0")" && pwd)"
SQL_DIR="$HERE/../../deploy/cnpj-db/sql"
PSQL="${PSQL:-docker exec -i cnpj-postgres psql -U cnpj}"
DB=vigilo_sql_teste
FAILS=0

q() { $PSQL -d "$DB" -X -q -At -v ON_ERROR_STOP=1 "$@"; }
check() { # check <descrição> <esperado> <sql>
    local got
    got="$(q -c "$3" 2>&1)"
    if [ "$got" = "$2" ]; then
        echo "  ok   $1"
    else
        echo "  FAIL $1"
        echo "       esperado: $2"
        echo "       obtido:   $got"
        FAILS=$((FAILS + 1))
    fi
}

$PSQL -d postgres -X -q -c "DROP DATABASE IF EXISTS $DB" -c "CREATE DATABASE $DB" >/dev/null || exit 1
trap '$PSQL -d postgres -X -q -c "DROP DATABASE IF EXISTS $DB" >/dev/null' EXIT

q <"$HERE/fixtures/pipeline-initial.sql" >/dev/null 2>&1 || { echo "!! schema do pipeline falhou"; exit 1; }

# --- Dump 2026-09 (primeira carga) -------------------------------------------
q >/dev/null <<'SQL'
INSERT INTO processed_files (directory, filename, processed_at) VALUES
    ('2026-09', 'Empresas0.zip', '2026-09-21 01:00'), ('2026-09', 'Socios0.zip', '2026-09-21 04:00'),
    ('2026-09', 'Socios1.zip', '2026-09-21 05:00');
INSERT INTO empresas (cnpj_basico, razao_social, natureza_juridica, capital_social, porte, data_criacao, data_atualizacao) VALUES
    ('11222333', 'PADARIA SÃO JOSÉ LTDA', '2062', 1000, '01', '2026-09-21 01:00', '2026-09-21 01:00'),
    ('44555666', 'JOSÉ DA SILVA 12345678901', '2135', 0, '01', '2026-09-21 01:00', '2026-09-21 01:00');
INSERT INTO estabelecimentos (cnpj_basico, cnpj_ordem, cnpj_dv, nome_fantasia, situacao_cadastral, logradouro, numero, cep, municipio, uf, cnae_fiscal_principal, data_criacao, data_atualizacao) VALUES
    ('11222333', '0001', '81', 'PÃO QUENTE', '02', 'RUA A', '10', '01001000', '7107', 'SP', '1091102', '2026-09-21 02:00', '2026-09-21 02:00'),
    ('44555666', '0001', '07', '', '02', 'RUA B', '20', '01002000', '7107', 'SP', '4712100', '2026-09-21 02:00', '2026-09-21 02:00');
INSERT INTO socios (socio_id, cnpj_basico, identificador_de_socio, nome_socio, cnpj_cpf_do_socio, qualificacao_do_socio, data_criacao, data_atualizacao) VALUES
    ('00000000-0000-0000-0000-00000000000a', '11222333', '2', 'ANA SOUZA', '***111222**', '49', '2026-09-21 04:00', '2026-09-21 04:00'),
    ('00000000-0000-0000-0000-00000000000b', '11222333', '2', 'BRUNO LIMA', '***333444**', '22', '2026-09-21 04:00', '2026-09-21 04:00');
SQL

for f in historico busca; do
    q <"$SQL_DIR/$f.sql" >/dev/null 2>&1 || { echo "!! $f.sql falhou"; q <"$SQL_DIR/$f.sql"; exit 1; }
done
# Idempotência: aplicar de novo não pode falhar.
for f in historico busca; do
    q <"$SQL_DIR/$f.sql" >/dev/null 2>&1 || { echo "!! $f.sql não é idempotente"; exit 1; }
done

# Carga inicial da busca (o mesmo INSERT do bin/busca-popular.sh).
q >/dev/null <<'SQL'
INSERT INTO busca_razao (cnpj_basico, nome)
    SELECT cnpj_basico, nome FROM (SELECT cnpj_basico, busca_norm(razao_social) AS nome FROM empresas WHERE razao_social <> '') x
    WHERE btrim(nome) <> '' ON CONFLICT (cnpj_basico) DO NOTHING;
INSERT INTO busca_fantasia (cnpj_basico, cnpj_ordem, cnpj_dv, nome)
    SELECT cnpj_basico, cnpj_ordem, cnpj_dv, nome FROM (SELECT cnpj_basico, cnpj_ordem, cnpj_dv, busca_norm(nome_fantasia) AS nome FROM estabelecimentos WHERE nome_fantasia <> '') x
    WHERE btrim(nome) <> '' ON CONFLICT (cnpj_basico, cnpj_ordem, cnpj_dv) DO NOTHING;
SQL

echo "cnpj-db sql"

check "busca_norm: sem acento, maiúsculas, espaço nas pontas" " PADARIA SAO JOSE LTDA " "SELECT busca_norm('Padaria São José Ltda')"
check "busca_norm: pontuação vira separador" " J NOVAIS CIA " "SELECT busca_norm('J.NOVAIS & CIA')"
check "busca_norm: CPF no fim da razão de MEI sai da chave" " JOSE DA SILVA " "SELECT busca_norm('JOSÉ DA SILVA 12345678901')"
check "busca_norm: vazio/nulo vira chave em branco" "t" "SELECT btrim(busca_norm(NULL)) = ''"
check "carga da busca: razão social (MEI sem o CPF)" " JOSE DA SILVA " "SELECT nome FROM busca_razao WHERE cnpj_basico = '44555666'"
check "carga da busca: nome fantasia vazio fica de fora" "1" "SELECT count(*) FROM busca_fantasia"
q -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport na primeira carga: só registra" "primeira_carga" "SELECT status FROM historico_execucoes WHERE referencia = '2026-09'"

# --- Dump 2026-10: upsert igual ao do pipeline --------------------------------
q >/dev/null <<'SQL'
-- empresa 1: razão social muda; empresa 2: igual; empresa 3: NOVA
INSERT INTO empresas (cnpj_basico, razao_social, natureza_juridica, capital_social, porte) VALUES
    ('11222333', 'PADARIA SÃO JOSÉ COMÉRCIO LTDA', '2062', 5000, '01'),
    ('44555666', 'JOSÉ DA SILVA 12345678901', '2135', 0, '01'),
    ('77888999', 'EMPRESA NOVA LTDA', '2062', 10, '01')
ON CONFLICT (cnpj_basico) DO UPDATE SET razao_social = EXCLUDED.razao_social, natureza_juridica = EXCLUDED.natureza_juridica,
    capital_social = EXCLUDED.capital_social, porte = EXCLUDED.porte, data_atualizacao = CURRENT_TIMESTAMP;

-- estab 1: situação 02→08 e endereço muda; estab 2: igual; estab 3: novo
INSERT INTO estabelecimentos (cnpj_basico, cnpj_ordem, cnpj_dv, nome_fantasia, situacao_cadastral, logradouro, numero, cep, municipio, uf, cnae_fiscal_principal) VALUES
    ('11222333', '0001', '81', 'PÃO QUENTE', '08', 'RUA C', '99', '01003000', '7107', 'SP', '1091102'),
    ('44555666', '0001', '07', '', '02', 'RUA B', '20', '01002000', '7107', 'SP', '4712100'),
    ('77888999', '0001', '55', 'NOVIDADE', '02', 'RUA D', '1', '01004000', '7107', 'SP', '4712100')
ON CONFLICT (cnpj_basico, cnpj_ordem, cnpj_dv) DO UPDATE SET nome_fantasia = EXCLUDED.nome_fantasia,
    situacao_cadastral = EXCLUDED.situacao_cadastral, logradouro = EXCLUDED.logradouro, numero = EXCLUDED.numero,
    cep = EXCLUDED.cep, municipio = EXCLUDED.municipio, uf = EXCLUDED.uf,
    cnae_fiscal_principal = EXCLUDED.cnae_fiscal_principal, data_atualizacao = CURRENT_TIMESTAMP;

-- sócios: ANA segue (reescrita), BRUNO saiu (não vem), CARLA entrou; DIEGO é da empresa nova
INSERT INTO socios (socio_id, cnpj_basico, identificador_de_socio, nome_socio, cnpj_cpf_do_socio, qualificacao_do_socio) VALUES
    ('00000000-0000-0000-0000-00000000000a', '11222333', '2', 'ANA SOUZA', '***111222**', '49'),
    ('00000000-0000-0000-0000-00000000000c', '11222333', '2', 'CARLA DIAS', '***555666**', '22'),
    ('00000000-0000-0000-0000-00000000000d', '77888999', '2', 'DIEGO REIS', '***777888**', '49')
ON CONFLICT (socio_id) DO UPDATE SET nome_socio = EXCLUDED.nome_socio, data_atualizacao = CURRENT_TIMESTAMP;

-- Como no pipeline: o arquivo só é marcado processado DEPOIS de gravado.
INSERT INTO processed_files (directory, filename, processed_at) VALUES
    ('2026-10', 'Empresas0.zip', clock_timestamp()), ('2026-10', 'Socios0.zip', clock_timestamp()), ('2026-10', 'Socios1.zip', clock_timestamp());
SQL

check "gatilho: situação 02→08" "02|08|0001" \
    "SELECT valor_anterior || '|' || valor_novo || '|' || cnpj_ordem FROM historico_cadastral WHERE campo = 'situacao'"
check "gatilho: endereço vira UM evento com o JSON novo" "1|RUA C|99" \
    "SELECT count(*) || '|' || max(valor_novo::jsonb ->> 'logradouro') || '|' || max(valor_novo::jsonb ->> 'numero') FROM historico_cadastral WHERE campo = 'endereco'"
check "gatilho: razão social e capital da empresa (evento sem ordem/dv)" "capital_social|razao_social" \
    "SELECT string_agg(campo, '|' ORDER BY campo) FROM historico_cadastral WHERE cnpj_basico = '11222333' AND cnpj_ordem IS NULL"
check "gatilho: linha reescrita sem mudança não gera evento" "0" \
    "SELECT count(*) FROM historico_cadastral WHERE cnpj_basico = '44555666'"
check "gatilho: inserção (empresa nova) não é 'mudança'" "0" \
    "SELECT count(*) FROM historico_cadastral WHERE cnpj_basico = '77888999'"
check "busca: razão social alterada é atualizada" " PADARIA SAO JOSE COMERCIO LTDA " "SELECT nome FROM busca_razao WHERE cnpj_basico = '11222333'"
check "busca: empresa nova entra na busca (razão e fantasia)" "1|1" \
    "SELECT (SELECT count(*) FROM busca_razao WHERE cnpj_basico = '77888999') || '|' || (SELECT count(*) FROM busca_fantasia WHERE cnpj_basico = '77888999')"
check "busca: LIKE de começo de palavra usa a chave normalizada" "11222333" \
    "SELECT cnpj_basico FROM busca_razao WHERE nome LIKE '% PADARIA%' AND nome LIKE '% JOSE%'"

# Base de teste minúscula: o limite de saídas (5% da base) abortaria sempre — aqui
# testamos a lógica; o limite tem um teste próprio no fim.
q -v max_saidas_pct=100 -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport: execução ok com 1 saída e 1 entrada" "ok|1|1" \
    "SELECT status || '|' || socios_saida || '|' || socios_entrada FROM historico_execucoes WHERE referencia = '2026-10'"
check "pos-reimport: sócio que saiu é apagado da base" "ANA SOUZA|CARLA DIAS|DIEGO REIS" \
    "SELECT string_agg(nome_socio, '|' ORDER BY nome_socio) FROM socios"
check "pos-reimport: saída registrada com o sócio antigo" "BRUNO LIMA" \
    "SELECT valor_anterior::jsonb ->> 'nome' FROM historico_cadastral WHERE campo = 'socio_saida'"
check "pos-reimport: entrada só em empresa que já existia (não a nova)" "CARLA DIAS" \
    "SELECT string_agg(valor_novo::jsonb ->> 'nome', '|') FROM historico_cadastral WHERE campo = 'socio_entrada'"
check "pos-reimport: carimba o dump em todos os eventos" "0" \
    "SELECT count(*) FROM historico_cadastral WHERE referencia IS DISTINCT FROM '2026-10'"

q -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport: re-rodar é no-op" "1|3" \
    "SELECT (SELECT count(*) FROM historico_execucoes WHERE referencia = '2026-10') || '|' || (SELECT count(*) FROM socios)"

# --- Dump 2026-11 incompleto: espera completar --------------------------------
q -c "INSERT INTO processed_files (directory, filename, processed_at) VALUES ('2026-11', 'Socios0.zip', now())" >/dev/null
q -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport: dump incompleto não roda (nada apagado)" "0|3" \
    "SELECT (SELECT count(*) FROM historico_execucoes WHERE referencia = '2026-11') || '|' || (SELECT count(*) FROM socios)"

# --- Sanidade: saídas demais abortam sem apagar --------------------------------
q -c "INSERT INTO processed_files (directory, filename, processed_at) VALUES ('2026-11', 'Socios1.zip', now())" >/dev/null
q -c "ANALYZE socios" >/dev/null
q -c "UPDATE estabelecimentos SET situacao_cadastral = '04', data_atualizacao = CURRENT_TIMESTAMP WHERE cnpj_basico = '44555666'" >/dev/null
q -v max_saidas_pct=5 -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport: saídas acima do limite abortam e não apagam nada" "abortado|3" \
    "SELECT (SELECT status FROM historico_execucoes WHERE referencia = '2026-11') || '|' || (SELECT count(*) FROM socios)"
check "pos-reimport abortado: eventos de gatilho do dump ainda são carimbados" "1" \
    "SELECT count(*) FROM historico_cadastral WHERE referencia = '2026-11' AND campo = 'situacao'"

# --- Sanidade: campo com mudanças demais num dump é descartado ---------------
q >/dev/null <<'SQL'
UPDATE estabelecimentos SET nome_fantasia = 'NOME NOVO', data_atualizacao = CURRENT_TIMESTAMP WHERE cnpj_basico = '77888999';
INSERT INTO processed_files (directory, filename, processed_at) VALUES
    ('2026-12', 'Socios0.zip', clock_timestamp()), ('2026-12', 'Socios1.zip', clock_timestamp());
SQL
q -v max_saidas_pct=100 -v max_eventos_campo=0 -f /dev/stdin <"$SQL_DIR/pos-reimport.sql" >/dev/null 2>&1
check "pos-reimport: campo acima de max_eventos_campo é descartado e anotado" "ok|0|true" \
    "SELECT status || '|' || (SELECT count(*) FROM historico_cadastral WHERE referencia = '2026-12' AND campo = 'nome_fantasia') || '|' || (observacao LIKE '%nome_fantasia%') FROM historico_execucoes WHERE referencia = '2026-12'"

check "busca_norm: número de 12 dígitos no fim não é CPF" " EMPRESA 123456789012 " "SELECT busca_norm('EMPRESA 123456789012')"
check "busca_norm: só o CPF vira chave em branco" "t" "SELECT btrim(busca_norm('12345678901')) = ''"

if [ "$FAILS" -gt 0 ]; then
    echo "$FAILS teste(s) falharam"
    exit 1
fi
echo "todos os testes passaram"
