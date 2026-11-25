# Fontes de dados — o que o Vigilo usa, o que foi avaliado e o que é pago

Atualizado em 2026-11-25 (roteiro "recuperação de bens"). Preços de tabela
informados pelos próprios fornecedores; **nada foi contratado**.

## Em uso (gratuitas, dados abertos)
| Fonte | Para quê | Onde mora |
|---|---|---|
| Receita Federal (CNPJ: empresas, estabelecimentos, sócios, Simples/MEI) | perfil, grafo, busca, pessoas, indícios | base CNPJ na VM (reimport mensal) |
| PGFN (dívida ativa da União, só PJ) | risco, marco dos indícios de esvaziamento | base CNPJ na VM (trimestral) |
| CGU (CEIS/CNEP/CEPIM) | sanções | cópia local no app (diária) |
| MTE (Lista Suja) | trabalho escravo | cópia local no app |
| Portal da Transparência (contratos por fornecedor) | contratos públicos, créditos a receber | API, cache 24 h |
| ANAC (Registro Aeronáutico Brasileiro) | aeronaves (bens) | cópia local no app (diária) |
| INPI (marcas, titular PJ) | marcas registradas (bens) | base CNPJ na VM (semanal) |

## Avaliadas e não implementadas
- **DataJud (CNJ, API pública de processos):**
  - **Instável:** em 2026-11-24 e 2026-11-25 respondeu 429 / `es_rejected_execution_exception` (sobrecarga).
  - **Sem busca por empresa:** a Portaria CNJ 374/2026 teria incluído "polo ativo e polo passivo, quando pessoas jurídicas" nos dados da API, mas CPF/CNPJ não é campo de busca indexado. Casar empresa ↔ processo seria só por nome, sujeito a homônimos.
  - **Documentação inacessível:** a wiki bloqueia cliente automatizado.
  - **Decisão:** não implementar agora. Reavaliar quando a API estabilizar e se o polo PJ vier pesquisável.
- **INPI BADEPI:** a suspeita de que não traria o CNPJ do titular **não se confirmou**: `MARCAS_DEPOSITANTES.csv` tem `cnpj_cpf_titular`. Implementada (marcas).

## Pagas — opções levantadas (sem contrato)
| Necessidade | Opção | Preço de tabela | Observação |
|---|---|---|---|
| Imóveis | ONR/SAEC "Pesquisa Nacional de Bens" (todo o país desde out/2026, ~3,6 mil cartórios) | emolumentos de cada UF consultada | resultado em < 1 min; não é certidão |
| Protestos | consulta gratuita em pesquisaprotesto.com.br (por CPF/CNPJ, sem API) | grátis (manual) | — |
| Protestos via API | Direct Data (homologada CENPROT) | R$ 2,95–3,50 por consulta | sem contrato mínimo (pré-pago) |
| Processos por CNPJ | Judit | contador R$ 0,50; histórico cobrado a cada 1.000 processos (Enterprise) | também consulta tribunais em tempo real (mais caro) |
| Processos por CNPJ | Escavador (Business), Digesto | sob proposta | — |
| Veículos | não há fonte que liste a frota de terceiro por CNPJ | — | Serpro e-Frotas só a frota da própria empresa; revendedores consultam por placa |
| % de participação no capital | contrato social (Juntas Comerciais / REDESIM) | por Junta | sem API nacional aberta |
