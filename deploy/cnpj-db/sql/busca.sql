-- Busca de empresas por NOME (razão social e nome fantasia) na base CNPJ.
--
-- Índice GIN sobre uma chave normalizada — maiúsculas, sem acento, só
-- letras/dígitos separados por espaço, com espaço nas pontas — para o app casar
-- começo de palavra com `nome LIKE '% TERMO%'` (trigram atende LIKE). A MESMA
-- normalização existe no PHP (App\Support\BuscaNome).
--
-- UF e situação ficam DENTRO das tabelas da busca e do índice (btree_gin): o
-- filtro "padaria em SE, ativas" resolve no índice, sem cruzar candidato a
-- candidato com os 73 M de estabelecimentos (isso estourava o timeout).
--
-- Fica em tabelas próprias, mantidas por gatilho SÓ quando nome/UF/situação
-- mudam: o upsert do pipeline reescreve as 70 M linhas de `empresas` todo mês,
-- e um GIN direto nela seria reescrito (e incharia) inteiro a cada dump.
--
-- CPF no fim da razão social de MEI ("FULANO DE TAL 12345678901") sai da chave:
-- não dá para buscar empresa por CPF.
--
-- Idempotente (bin/aplicar-sql.sh, antes de cada reimport). A carga inicial das
-- tabelas é o bin/busca-popular.sh (uma vez; depois os gatilhos mantêm).

CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE EXTENSION IF NOT EXISTS unaccent;
CREATE EXTENSION IF NOT EXISTS btree_gin;

CREATE OR REPLACE FUNCTION busca_norm(t text) RETURNS text
LANGUAGE sql IMMUTABLE PARALLEL SAFE AS $$
    SELECT ' ' || btrim(regexp_replace(
        -- só um número de EXATAMENTE 11 dígitos no fim (CPF de MEI): "… 123456789012" fica
        regexp_replace(upper(public.unaccent('public.unaccent'::regdictionary, coalesce(t, ''))), '(^|[^0-9])[0-9]{11}[[:space:]]*$', '\1'),
        '[^A-Z0-9]+', ' ', 'g'
    )) || ' '
$$;

-- Razão social → uma linha por empresa; UF/situação são as da MATRIZ.
CREATE TABLE IF NOT EXISTS busca_razao (
    cnpj_basico varchar(8) PRIMARY KEY,
    nome text NOT NULL,
    uf varchar(2),
    situacao varchar(2)
);

CREATE TABLE IF NOT EXISTS busca_fantasia (
    cnpj_basico varchar(8) NOT NULL,
    cnpj_ordem varchar(4) NOT NULL,
    cnpj_dv varchar(2) NOT NULL,
    nome text NOT NULL,
    uf varchar(2),
    situacao varchar(2),
    PRIMARY KEY (cnpj_basico, cnpj_ordem, cnpj_dv)
);

-- Bases criadas antes das colunas de filtro (a carga do busca-popular.sh preenche).
ALTER TABLE busca_razao ADD COLUMN IF NOT EXISTS uf varchar(2);
ALTER TABLE busca_razao ADD COLUMN IF NOT EXISTS situacao varchar(2);
ALTER TABLE busca_fantasia ADD COLUMN IF NOT EXISTS uf varchar(2);
ALTER TABLE busca_fantasia ADD COLUMN IF NOT EXISTS situacao varchar(2);

-- Empresa nova / razão social alterada. UF e situação vêm da matriz, se ela já
-- estiver na base (o pipeline grava empresas e estabelecimentos em arquivos
-- separados; se a matriz chegar depois, o gatilho dela completa).
CREATE OR REPLACE FUNCTION busca_empresa() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE
    m_uf varchar(2);
    m_situacao varchar(2);
BEGIN
    IF btrim(busca_norm(NEW.razao_social)) = '' THEN
        DELETE FROM busca_razao WHERE cnpj_basico = NEW.cnpj_basico;
        RETURN NULL;
    END IF;

    SELECT e.uf, e.situacao_cadastral INTO m_uf, m_situacao
        FROM estabelecimentos e
        WHERE e.cnpj_basico = NEW.cnpj_basico AND e.identificador_matriz_filial = 1
        LIMIT 1;

    INSERT INTO busca_razao (cnpj_basico, nome, uf, situacao)
        VALUES (NEW.cnpj_basico, busca_norm(NEW.razao_social), m_uf, m_situacao)
        ON CONFLICT (cnpj_basico) DO UPDATE SET nome = EXCLUDED.nome;

    RETURN NULL;
END
$$;

CREATE OR REPLACE TRIGGER trg_busca_empresa_ins
    AFTER INSERT ON empresas
    FOR EACH ROW EXECUTE FUNCTION busca_empresa();

CREATE OR REPLACE TRIGGER trg_busca_empresa_upd
    AFTER UPDATE ON empresas
    FOR EACH ROW
    WHEN (OLD.razao_social IS DISTINCT FROM NEW.razao_social)
    EXECUTE FUNCTION busca_empresa();

-- Estabelecimento novo / nome fantasia, UF ou situação alterados: mantém a sua
-- linha em busca_fantasia e, se for a matriz, a UF/situação da razão social.
CREATE OR REPLACE FUNCTION busca_estabelecimento() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF btrim(busca_norm(NEW.nome_fantasia)) = '' THEN
        DELETE FROM busca_fantasia
            WHERE cnpj_basico = NEW.cnpj_basico AND cnpj_ordem = NEW.cnpj_ordem AND cnpj_dv = NEW.cnpj_dv;
    ELSE
        INSERT INTO busca_fantasia (cnpj_basico, cnpj_ordem, cnpj_dv, nome, uf, situacao)
            VALUES (NEW.cnpj_basico, NEW.cnpj_ordem, NEW.cnpj_dv, busca_norm(NEW.nome_fantasia), NEW.uf, NEW.situacao_cadastral)
            ON CONFLICT (cnpj_basico, cnpj_ordem, cnpj_dv)
            DO UPDATE SET nome = EXCLUDED.nome, uf = EXCLUDED.uf, situacao = EXCLUDED.situacao;
    END IF;

    IF NEW.identificador_matriz_filial = 1 THEN
        UPDATE busca_razao SET uf = NEW.uf, situacao = NEW.situacao_cadastral
            WHERE cnpj_basico = NEW.cnpj_basico
              AND (uf IS DISTINCT FROM NEW.uf OR situacao IS DISTINCT FROM NEW.situacao_cadastral);
    END IF;

    RETURN NULL;
END
$$;

CREATE OR REPLACE TRIGGER trg_busca_estabelecimento_ins
    AFTER INSERT ON estabelecimentos
    FOR EACH ROW
    WHEN ((NEW.nome_fantasia IS NOT NULL AND NEW.nome_fantasia <> '') OR NEW.identificador_matriz_filial = 1)
    EXECUTE FUNCTION busca_estabelecimento();

CREATE OR REPLACE TRIGGER trg_busca_estabelecimento_upd
    AFTER UPDATE ON estabelecimentos
    FOR EACH ROW
    WHEN (OLD.nome_fantasia IS DISTINCT FROM NEW.nome_fantasia
       OR OLD.uf IS DISTINCT FROM NEW.uf
       OR OLD.situacao_cadastral IS DISTINCT FROM NEW.situacao_cadastral)
    EXECUTE FUNCTION busca_estabelecimento();

-- Índices de busca: UF + situação (btree_gin) + chave trigram no MESMO GIN, para
-- o filtro andar junto com o LIKE. Na base em produção eles já existem (criados
-- pelo busca-popular.sh DEPOIS da carga, que é bem mais rápido); aqui só
-- garantem que uma base nova/restaurada sem eles volte a tê-los. Os índices
-- antigos (só trigram) saem.
DROP INDEX IF EXISTS idx_busca_razao_trgm;
DROP INDEX IF EXISTS idx_busca_fantasia_trgm;
CREATE INDEX IF NOT EXISTS idx_busca_razao_gin ON busca_razao USING gin (uf, situacao, nome gin_trgm_ops);
CREATE INDEX IF NOT EXISTS idx_busca_fantasia_gin ON busca_fantasia USING gin (uf, situacao, nome gin_trgm_ops);
