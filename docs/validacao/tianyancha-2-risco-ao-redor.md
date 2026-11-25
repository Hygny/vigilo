# Validação — Roteiro Tianyancha 2/4 · Selo de risco ao redor (+ risco do perfil público)

- **Data:** 2026-11-25
- **Ciclo:** 1
- **Veredito:** APROVADO — **85/100** (`acao: seguir`), sem blockers
- **Portões (Juiz):** Pint ok · PHPStan nível 8 ok · Pest 630/630 (633/633 após as ressalvas) · migrations reversíveis

## Escopo
- **Cópia local das sanções da CGU** (`vigilo:sancoes-cgu`, agendado 08:30/14:30 BRT): CEIS/CNEP/CEPIM do "download de dados" do Portal da Transparência (sem chave), só PJ, Latin-1→UTF-8, recarga atômica por fonte, `cgu_cargas` com a data do arquivo; nunca apaga a cópia boa com arquivo vazio. Rodado contra os arquivos reais: 7 s, 14.572 / 1.828 / 3.514 sanções de PJ.
- **`SancoesLocais`**: consulta em lote pela raiz; "sem sanção" só com as 3 fontes carregadas; data da cópia + "desatualizada".
- **Card "Risco" no perfil público** (`Empresas\RiscoPerfil`, lazy) pela mesma regra da carteira (`RiscoAssessor::paraPublico`).
- **Risco ao redor** (`RiscoAoRedor`): ligadas por relação certa × PGFN / sanção vigente / Lista Suja / situação negativa; selo "N de M empresas ligadas com …"; no perfil e no painel da carteira.
- **Lista Suja pela raiz do CNPJ** (filial listada conta), preferindo o CNPJ exato — o alerta da carteira ganha esse recall.

## Achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | "Nenhuma ligada com sinal" em verde mesmo com sanções/PGFN não consultadas | ✅ Verde só com `semSinalConfiavel()` (todas as fontes + cópia em dia); senão tom neutro "nas fontes que puderam ser consultadas" + teste |
| A2 | baixa | Cópia desatualizada ainda dava "Risco Baixo" com motivo de limpo; risco ao redor não avisava | ✅ Ressalva no motivo do Baixo do perfil público; `sancoesDesatualizadas` no relatório ao redor (aviso + sem verde) + teste |
| A3 | baixa | Faltavam testes: tela "não consultado", 403/404 do `RiscoPerfil`, `withoutOverlapping`, alerta do `DirtyListMonitor` via filial | ✅ Os quatro casos |
| A4 | baixa | Helper `tentar()` copiado em 3 classes | ✅ `App\Support\Tentar::ouNull()` |
| A5 | baixa | Risco ao redor remonta o grafo a cada render, sem cache | ➖ **DT-31** (lazy + capado; cache curto se pesar) |
