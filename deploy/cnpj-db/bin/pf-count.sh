#!/usr/bin/env bash
#
# Quantos arquivos da Receita o pipeline já processou. O VPS compara antes e
# depois do reimport para saber se veio dump novo (verbo `pf-count`).
set -euo pipefail

docker exec cnpj-postgres psql -U cnpj -d cnpj -tAc "SELECT count(*) FROM processed_files" </dev/null
