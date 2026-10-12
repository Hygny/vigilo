#!/usr/bin/env python3
"""Filtra um ZIP da Dívida Ativa (PGFN) pela carteira, em STREAMING.

Lê cada CSV de dentro do zip sem descompactar o arquivo inteiro em disco, mantém
só as linhas cujo documento (coluna CPF_CNPJ) — depois de tirar a pontuação — é
um CNPJ de 14 dígitos que está na carteira, e escreve um CSV pequeno com o
cabeçalho uma vez. É a MESMA regra do importador PHP (tira não-dígitos), então é
imune ao formato do dump (com ou sem pontuação): não descarta inscrição da
carteira por diferença de máscara. Usa só a stdlib (zipfile) — sem `unzip`.

Uso:
    pgfn_filter.py <zip> <carteira.txt> <saida.csv> [delimitador]

`carteira.txt`: um documento por linha (a pontuação é ignorada). Sai com código
!= 0 em qualquer erro, para o chamador NÃO importar dados parciais.
"""
from __future__ import annotations

import io
import re
import sys
import zipfile

_ONLY_DIGITS = re.compile(r"\D")


def digits(value: str) -> str:
    return _ONLY_DIGITS.sub("", value or "")


def find_doc_column(header: list[str], terms: tuple[str, ...]) -> int | None:
    low = [h.strip().lower() for h in header]
    for i, name in enumerate(low):  # preferência: cabeçalho exatamente igual
        if name in terms:
            return i
    for i, name in enumerate(low):  # senão, o primeiro que contém o termo
        if any(term in name for term in terms):
            return i
    return None


def main(argv: list[str]) -> int:
    if len(argv) < 4:
        print("uso: pgfn_filter.py <zip> <carteira.txt> <saida.csv> [delim]", file=sys.stderr)
        return 2

    zip_path, carteira_path, out_path = argv[1], argv[2], argv[3]
    delim = argv[4] if len(argv) > 4 else ";"

    with open(carteira_path, encoding="utf-8") as fh:
        carteira = {d for line in fh if (d := digits(line))}

    if not carteira:
        print("carteira vazia", file=sys.stderr)
        return 1

    kept = 0
    with zipfile.ZipFile(zip_path) as zf, open(out_path, "w", encoding="latin-1", newline="") as out:
        members = [n for n in zf.namelist() if n.lower().endswith(".csv")]
        if not members:
            print("nenhum .csv dentro do zip", file=sys.stderr)
            return 1

        first_header: list[str] | None = None
        for name in members:
            with zf.open(name) as raw:
                stream = io.TextIOWrapper(raw, encoding="latin-1", newline="")
                first = stream.readline()
                if not first:
                    continue

                header = first.rstrip("\r\n").split(delim)

                # Cabeçalho com uma coluna só = delimitador errado. Falha-seguro:
                # sem isto, "0 linhas casam" seria confundido com "carteira limpa"
                # e, com --allow-empty, LIMPARIA o tipo em vez de abortar.
                if len(header) < 2:
                    print(f"cabeçalho com uma coluna só em {name} — delimitador '{delim}' errado?", file=sys.stderr)
                    return 1

                doc = find_doc_column(header, ("cnpj", "cpf", "documento"))
                if doc is None:
                    print(f"sem coluna de documento em {name}", file=sys.stderr)
                    return 1

                if first_header is None:
                    first_header = header
                    out.write(first if first.endswith("\n") else first + "\n")
                elif header != first_header:
                    # O PHP reinterpreta a saída concatenada sob UM cabeçalho; se as
                    # colunas divergirem entre CSVs, leria documento/valor da posição
                    # errada. O dump da PGFN é uniforme por UF — mas garantimos.
                    print(f"layout de colunas divergente em {name} (esperava o mesmo cabeçalho do 1º CSV)", file=sys.stderr)
                    return 1

                for line in stream:
                    cols = line.rstrip("\r\n").split(delim)
                    if doc < len(cols) and digits(cols[doc]) in carteira:
                        out.write(line if line.endswith("\n") else line + "\n")
                        kept += 1

    print(kept)
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv))
