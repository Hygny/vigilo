-- Histórico cadastral de QUALQUER empresa da base CNPJ (não só da carteira).
--
-- O pipeline da Receita faz upsert: cada dump reescreve as linhas, e o valor
-- antigo some. Os gatilhos abaixo guardam, no momento do upsert, só o que
-- MUDOU (situação, endereço, CNAE, nome fantasia, razão social, natureza,
-- capital, porte) em `historico_cadastral`. As entradas e saídas de sócios
-- vêm do pos-reimport.sql (o upsert nunca apaga sócio que saiu).
--
-- Idempotente: aplicado pelo bin/aplicar-sql.sh antes de cada reimport.
-- Custo no reimport: a condição WHEN é avaliada por linha em C (barata); a
-- função só roda para as linhas que mudaram de fato.

CREATE TABLE IF NOT EXISTS historico_cadastral (
    id bigserial PRIMARY KEY,
    cnpj_basico varchar(8) NOT NULL,
    -- ordem/dv nulos = evento da EMPRESA (razão social, capital, sócios...);
    -- preenchidos = evento de um estabelecimento (matriz ou filial).
    cnpj_ordem varchar(4),
    cnpj_dv varchar(2),
    campo varchar(40) NOT NULL,
    valor_anterior text,
    valor_novo text,
    detectado_em timestamptz NOT NULL DEFAULT now(),
    -- Dump da Receita (AAAA-MM) em que a mudança apareceu; carimbado pelo
    -- pos-reimport.sql ao fim de cada reimport.
    referencia varchar(7)
);

CREATE INDEX IF NOT EXISTS idx_historico_basico ON historico_cadastral (cnpj_basico, detectado_em DESC);
CREATE INDEX IF NOT EXISTS idx_historico_sem_referencia ON historico_cadastral (id) WHERE referencia IS NULL;

-- Rodadas do pos-reimport.sql (uma por dump): idempotência e auditoria.
CREATE TABLE IF NOT EXISTS historico_execucoes (
    referencia varchar(7) PRIMARY KEY,
    status varchar(20) NOT NULL,
    corte timestamp,
    socios_saida bigint NOT NULL DEFAULT 0,
    socios_entrada bigint NOT NULL DEFAULT 0,
    eventos bigint NOT NULL DEFAULT 0,
    observacao text,
    executado_em timestamptz NOT NULL DEFAULT now()
);

-- Endereço como um valor só (JSON): uma mudança de endereço é UM evento, e o app
-- formata a partir dos campos.
CREATE OR REPLACE FUNCTION historico_endereco(
    tipo text, logradouro text, numero text, complemento text,
    bairro text, cep text, municipio text, uf text
) RETURNS text LANGUAGE sql IMMUTABLE PARALLEL SAFE AS $$
    SELECT jsonb_build_object(
        'tipo_logradouro', tipo, 'logradouro', logradouro, 'numero', numero,
        'complemento', complemento, 'bairro', bairro, 'cep', cep,
        'municipio', municipio, 'uf', uf
    )::text
$$;

CREATE OR REPLACE FUNCTION historico_estabelecimento() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    INSERT INTO historico_cadastral (cnpj_basico, cnpj_ordem, cnpj_dv, campo, valor_anterior, valor_novo)
    SELECT NEW.cnpj_basico, NEW.cnpj_ordem, NEW.cnpj_dv, c.campo, c.antes, c.depois
    FROM (VALUES
        ('situacao', OLD.situacao_cadastral::text, NEW.situacao_cadastral::text),
        ('motivo_situacao', OLD.motivo_situacao_cadastral::text, NEW.motivo_situacao_cadastral::text),
        ('endereco',
            historico_endereco(OLD.tipo_logradouro, OLD.logradouro, OLD.numero, OLD.complemento, OLD.bairro, OLD.cep, OLD.municipio, OLD.uf),
            historico_endereco(NEW.tipo_logradouro, NEW.logradouro, NEW.numero, NEW.complemento, NEW.bairro, NEW.cep, NEW.municipio, NEW.uf)),
        ('cnae_principal', OLD.cnae_fiscal_principal::text, NEW.cnae_fiscal_principal::text),
        ('cnae_secundaria', OLD.cnae_fiscal_secundaria, NEW.cnae_fiscal_secundaria),
        ('nome_fantasia', OLD.nome_fantasia, NEW.nome_fantasia)
    ) AS c(campo, antes, depois)
    WHERE c.antes IS DISTINCT FROM c.depois;

    RETURN NULL;
END
$$;

CREATE OR REPLACE TRIGGER trg_historico_estabelecimento
    AFTER UPDATE ON estabelecimentos
    FOR EACH ROW
    WHEN (OLD.situacao_cadastral IS DISTINCT FROM NEW.situacao_cadastral
       OR OLD.motivo_situacao_cadastral IS DISTINCT FROM NEW.motivo_situacao_cadastral
       OR OLD.tipo_logradouro IS DISTINCT FROM NEW.tipo_logradouro
       OR OLD.logradouro IS DISTINCT FROM NEW.logradouro
       OR OLD.numero IS DISTINCT FROM NEW.numero
       OR OLD.complemento IS DISTINCT FROM NEW.complemento
       OR OLD.bairro IS DISTINCT FROM NEW.bairro
       OR OLD.cep IS DISTINCT FROM NEW.cep
       OR OLD.municipio IS DISTINCT FROM NEW.municipio
       OR OLD.uf IS DISTINCT FROM NEW.uf
       OR OLD.cnae_fiscal_principal IS DISTINCT FROM NEW.cnae_fiscal_principal
       OR OLD.cnae_fiscal_secundaria IS DISTINCT FROM NEW.cnae_fiscal_secundaria
       OR OLD.nome_fantasia IS DISTINCT FROM NEW.nome_fantasia)
    EXECUTE FUNCTION historico_estabelecimento();

CREATE OR REPLACE FUNCTION historico_empresa() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    INSERT INTO historico_cadastral (cnpj_basico, campo, valor_anterior, valor_novo)
    SELECT NEW.cnpj_basico, c.campo, c.antes, c.depois
    FROM (VALUES
        ('razao_social', OLD.razao_social, NEW.razao_social),
        ('natureza_juridica', OLD.natureza_juridica::text, NEW.natureza_juridica::text),
        ('capital_social', OLD.capital_social::text, NEW.capital_social::text),
        ('porte', OLD.porte::text, NEW.porte::text)
    ) AS c(campo, antes, depois)
    WHERE c.antes IS DISTINCT FROM c.depois;

    RETURN NULL;
END
$$;

CREATE OR REPLACE TRIGGER trg_historico_empresa
    AFTER UPDATE ON empresas
    FOR EACH ROW
    WHEN (OLD.razao_social IS DISTINCT FROM NEW.razao_social
       OR OLD.natureza_juridica IS DISTINCT FROM NEW.natureza_juridica
       OR OLD.capital_social IS DISTINCT FROM NEW.capital_social
       OR OLD.porte IS DISTINCT FROM NEW.porte)
    EXECUTE FUNCTION historico_empresa();
