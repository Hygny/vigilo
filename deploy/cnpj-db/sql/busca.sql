-- Busca de empresas por NOME (razão social e nome fantasia) na base CNPJ.
--
-- Índice trigram (pg_trgm) sobre uma chave normalizada — maiúsculas, sem
-- acento, só letras/dígitos separados por espaço, com espaço nas pontas — para
-- o app casar começo de palavra com `nome LIKE '% TERMO%'` (o GIN trigram
-- atende LIKE). A MESMA normalização existe no PHP (App\Support\BuscaNome).
--
-- Fica em tabelas próprias, mantidas por gatilho SÓ quando o nome muda: o
-- upsert do pipeline reescreve as 70 M linhas de `empresas` todo mês, e um GIN
-- direto nela seria reescrito (e incharia) inteiro a cada dump.
--
-- CPF no fim da razão social de MEI ("FULANO DE TAL 12345678901") sai da chave:
-- não dá para buscar empresa por CPF.
--
-- Idempotente (bin/aplicar-sql.sh, antes de cada reimport). A carga inicial das
-- tabelas é o bin/busca-popular.sh (uma vez; depois os gatilhos mantêm).

CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE EXTENSION IF NOT EXISTS unaccent;

CREATE OR REPLACE FUNCTION busca_norm(t text) RETURNS text
LANGUAGE sql IMMUTABLE PARALLEL SAFE AS $$
    SELECT ' ' || btrim(regexp_replace(
        -- só um número de EXATAMENTE 11 dígitos no fim (CPF de MEI): "… 123456789012" fica
        regexp_replace(upper(public.unaccent('public.unaccent'::regdictionary, coalesce(t, ''))), '(^|[^0-9])[0-9]{11}[[:space:]]*$', '\1'),
        '[^A-Z0-9]+', ' ', 'g'
    )) || ' '
$$;

CREATE TABLE IF NOT EXISTS busca_razao (
    cnpj_basico varchar(8) PRIMARY KEY,
    nome text NOT NULL
);

CREATE TABLE IF NOT EXISTS busca_fantasia (
    cnpj_basico varchar(8) NOT NULL,
    cnpj_ordem varchar(4) NOT NULL,
    cnpj_dv varchar(2) NOT NULL,
    nome text NOT NULL,
    PRIMARY KEY (cnpj_basico, cnpj_ordem, cnpj_dv)
);

CREATE OR REPLACE FUNCTION busca_empresa() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF btrim(busca_norm(NEW.razao_social)) = '' THEN
        DELETE FROM busca_razao WHERE cnpj_basico = NEW.cnpj_basico;
    ELSE
        INSERT INTO busca_razao (cnpj_basico, nome) VALUES (NEW.cnpj_basico, busca_norm(NEW.razao_social))
            ON CONFLICT (cnpj_basico) DO UPDATE SET nome = EXCLUDED.nome;
    END IF;

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

CREATE OR REPLACE FUNCTION busca_estabelecimento() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF btrim(busca_norm(NEW.nome_fantasia)) = '' THEN
        DELETE FROM busca_fantasia
            WHERE cnpj_basico = NEW.cnpj_basico AND cnpj_ordem = NEW.cnpj_ordem AND cnpj_dv = NEW.cnpj_dv;
    ELSE
        INSERT INTO busca_fantasia (cnpj_basico, cnpj_ordem, cnpj_dv, nome)
            VALUES (NEW.cnpj_basico, NEW.cnpj_ordem, NEW.cnpj_dv, busca_norm(NEW.nome_fantasia))
            ON CONFLICT (cnpj_basico, cnpj_ordem, cnpj_dv) DO UPDATE SET nome = EXCLUDED.nome;
    END IF;

    RETURN NULL;
END
$$;

CREATE OR REPLACE TRIGGER trg_busca_estabelecimento_ins
    AFTER INSERT ON estabelecimentos
    FOR EACH ROW
    WHEN (NEW.nome_fantasia IS NOT NULL AND NEW.nome_fantasia <> '')
    EXECUTE FUNCTION busca_estabelecimento();

CREATE OR REPLACE TRIGGER trg_busca_estabelecimento_upd
    AFTER UPDATE ON estabelecimentos
    FOR EACH ROW
    WHEN (OLD.nome_fantasia IS DISTINCT FROM NEW.nome_fantasia)
    EXECUTE FUNCTION busca_estabelecimento();

-- Índices de busca. Na base em produção eles já existem (criados pelo
-- busca-popular.sh DEPOIS da carga, que é bem mais rápido); aqui só garantem
-- que uma base nova/restaurada sem eles volte a tê-los.
CREATE INDEX IF NOT EXISTS idx_busca_razao_trgm ON busca_razao USING gin (nome gin_trgm_ops);
CREATE INDEX IF NOT EXISTS idx_busca_fantasia_trgm ON busca_fantasia USING gin (nome gin_trgm_ops);
