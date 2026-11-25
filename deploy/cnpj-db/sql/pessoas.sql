-- Busca de PESSOAS (sócios PF e estrangeiros) por nome — ver bin/pessoas-popular.sh,
-- que monta e troca a tabela a cada dump. Aqui só o esqueleto vazio (base nova,
-- restaurada ou de teste), para o app nunca bater em "tabela não existe" antes
-- da 1ª carga. Idempotente (bin/aplicar-sql.sh). Depende de busca.sql
-- (pg_trgm, busca_norm).
--
-- chave: uuid do md5(documento || '|' || nome_exibicao) — identificador opaco
--        da pessoa provável (vai na URL no lugar do nome/CPF).
-- nome: busca_norm(nome_exibicao) — maiúsculas, sem acento, espaço nas pontas.
-- documento: CPF mascarado (***NNNNNN**) ou '' (estrangeiro sem documento).
-- tipo: identificador_de_socio ('2' PF, '3' estrangeiro).

CREATE TABLE IF NOT EXISTS busca_pessoa (
    chave uuid PRIMARY KEY,
    nome text NOT NULL,
    nome_exibicao text NOT NULL,
    documento text NOT NULL,
    tipo varchar(1),
    empresas integer NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_busca_pessoa_gin ON busca_pessoa USING gin (nome gin_trgm_ops);
CREATE INDEX IF NOT EXISTS idx_busca_pessoa_nome ON busca_pessoa (nome text_pattern_ops);

-- Qual dump (assinatura de processed_files) a tabela reflete: o reimport só
-- remonta quando o dump mudou (pessoas-popular.sh --se-mudou).
CREATE TABLE IF NOT EXISTS busca_pessoa_cargas (
    id smallint PRIMARY KEY DEFAULT 1 CHECK (id = 1),
    assinatura text NOT NULL,
    pessoas bigint NOT NULL,
    montado_em timestamptz NOT NULL DEFAULT now()
);
