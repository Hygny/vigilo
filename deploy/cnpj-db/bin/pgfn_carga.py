#!/usr/bin/env python3
"""Converte os ZIPs da Dívida Ativa da União (PGFN) num CSV pronto para COPY.

Lê cada CSV de dentro dos zips em STREAMING (stdlib zipfile — nada é
descompactado em disco; o "Não Previdenciário" tem ~10 GB aberto), mantém só
devedores PESSOA JURÍDICA (documento com 14 dígitos depois de tirar a
pontuação) e escreve no stdout, em CSV UTF-8, as colunas da tabela de carga:

    cnpj, cnpj_basico, tipo, tipo_devedor, numero_inscricao, tipo_situacao,
    situacao, receita, data_inscricao (ISO), ajuizado (t/f), valor

CPF fica de fora de propósito: o Vigilo avalia empresas (LGPD).

Formato do dump (validado no 2026_trimestre_03): Latin-1, `;`, sem aspas, CNPJ
formatado (`10.496.760/0001-95`), data dd/mm/aaaa, valor com ponto decimal,
INDICADOR_AJUIZADO SIM/NAO, vários CSVs (um por UF/lote) dentro de cada zip.

Uso:
    pgfn_carga.py <tipo>=<zip> [<tipo>=<zip> ...] > carga.csv

A contagem por tipo vai para o stderr. Sai != 0 em qualquer erro (cabeçalho
diferente do esperado, zip corrompido, linhas tortas demais) — o chamador NÃO
troca a tabela com dado parcial.
"""
from __future__ import annotations

import csv
import io
import re
import sys
import zipfile

# Colunas que usamos → nomes EXATOS aceitos no cabeçalho (o 1º presente vale).
# Layout mudou = falha alta, em vez de carregar valor da coluna errada. O
# previdenciário chama a receita de TIPO_CREDITO; os outros, RECEITA_PRINCIPAL.
REQUIRED = {
    "documento": ("CPF_CNPJ",),
    "tipo_devedor": ("TIPO_DEVEDOR",),
    "numero": ("NUMERO_INSCRICAO",),
    "tipo_situacao": ("TIPO_SITUACAO_INSCRICAO",),
    "situacao": ("SITUACAO_INSCRICAO",),
    "receita": ("RECEITA_PRINCIPAL", "TIPO_CREDITO"),
    "data": ("DATA_INSCRICAO",),
    "ajuizado": ("INDICADOR_AJUIZADO",),
    "valor": ("VALOR_CONSOLIDADO",),
}

# Tolerância a linha torta (nº de colunas ≠ cabeçalho): acima disto, aborta.
MAX_BAD_RATIO = 0.001

_NON_DIGITS = re.compile(r"\D")
_DATE = re.compile(r"^(\d{2})/(\d{2})/(\d{4})$")


def parse_valor(raw: str) -> str:
    """Valor em ponto decimal ("5177.43"); com vírgula, trata como pt-BR ("1.234,56")."""
    value = raw.strip()
    if value == "":
        return ""
    if "," in value:
        value = value.replace(".", "").replace(",", ".")
    float(value)  # ValueError → linha torta
    return value


def parse_data(raw: str) -> str:
    match = _DATE.match(raw.strip())
    if not match:
        return ""
    dia, mes, ano = match.groups()
    # O dump usa 01/01/1000 como "sem data".
    if int(ano) < 1900 or not (1 <= int(mes) <= 12 and 1 <= int(dia) <= 31):
        return ""
    return f"{ano}-{mes}-{dia}"


def carregar(tipo: str, zip_path: str, out: csv.writer) -> tuple[int, int, int]:
    """Escreve as linhas PJ de um zip. Devolve (lidas, gravadas, tortas)."""
    lidas = gravadas = tortas = 0

    with zipfile.ZipFile(zip_path) as zf:
        members = [n for n in zf.namelist() if n.lower().endswith(".csv")]
        if not members:
            raise ValueError(f"{zip_path}: nenhum .csv dentro do zip")

        for name in members:
            with zf.open(name) as raw:
                stream = io.TextIOWrapper(raw, encoding="latin-1", newline="")
                header = stream.readline().rstrip("\r\n").split(";")
                idx: dict[str, int] = {}
                for campo, nomes in REQUIRED.items():
                    presentes = [n for n in nomes if n in header]
                    if not presentes:
                        raise ValueError(f"{name}: coluna ausente no cabeçalho: {' ou '.join(nomes)}")
                    idx[campo] = header.index(presentes[0])
                width = len(header)

                for line in stream:
                    cols = line.rstrip("\r\n").split(";")
                    lidas += 1

                    if len(cols) != width:
                        tortas += 1
                        continue

                    doc = _NON_DIGITS.sub("", cols[idx["documento"]])
                    if len(doc) != 14:
                        continue  # pessoa física (ou documento inválido)

                    try:
                        valor = parse_valor(cols[idx["valor"]])
                    except ValueError:
                        tortas += 1
                        continue

                    out.writerow((
                        doc,
                        doc[:8],
                        tipo,
                        cols[idx["tipo_devedor"]].strip().upper(),  # "Principal" × "PRINCIPAL" entre sistemas
                        cols[idx["numero"]].strip(),
                        cols[idx["tipo_situacao"]].strip(),
                        cols[idx["situacao"]].strip(),
                        cols[idx["receita"]].strip(),
                        parse_data(cols[idx["data"]]),
                        "t" if cols[idx["ajuizado"]].strip().upper() == "SIM" else "f",
                        valor,
                    ))
                    gravadas += 1

    return lidas, gravadas, tortas


def main(argv: list[str]) -> int:
    pares = argv[1:]
    if not pares or any("=" not in p for p in pares):
        print("uso: pgfn_carga.py <tipo>=<zip> [<tipo>=<zip> ...]", file=sys.stderr)
        return 2

    out = csv.writer(sys.stdout, lineterminator="\n")

    for par in pares:
        tipo, zip_path = par.split("=", 1)
        try:
            lidas, gravadas, tortas = carregar(tipo, zip_path, out)
        except (OSError, ValueError, zipfile.BadZipFile) as exc:
            print(f"!! {tipo}: {exc}", file=sys.stderr)
            return 1

        if lidas == 0:
            print(f"!! {tipo}: zip sem nenhuma linha de dado", file=sys.stderr)
            return 1

        if tortas / lidas > MAX_BAD_RATIO:
            print(f"!! {tipo}: {tortas} de {lidas} linhas tortas — layout mudou?", file=sys.stderr)
            return 1

        print(f"{tipo}: {lidas} inscrições lidas, {gravadas} de PJ gravadas, {tortas} tortas ignoradas", file=sys.stderr)

    sys.stdout.flush()
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv))
