# Migração da base CNPJ para a Oracle Cloud (Always Free)

Roteiro para tirar a base CNPJ (PostgreSQL) do VPS e levá-la para uma VM
gratuita da Oracle Cloud, liberando o disco do VPS. O app continua no VPS; só
muda o **endereço** da conexão `cnpj`.

> Quem faz o quê: as etapas marcadas **[você]** acontecem no console da Oracle
> (conta, VM, rede) — só você tem esse acesso. As marcadas **[Claude]** eu faço
> por SSH depois que você me passar o IP da VM. Etapas no VPS que mudam algo
> (cron, `.env`, apagar volume) eu só executo com o seu ok **na hora**.

---

## ✅ Resultado — migração concluída em 2026-10-23

Todas as fases rodaram no mesmo dia (o usuário autorizou seguir sem pausas):

| Fase | Resultado |
|---|---|
| 1 · VM | provisionada e testada com reboot; 5432 só do VPS, TLS `verify-full` |
| 2 · Cópia | `pg_dump` zstd em **15 min** (7,6 GB) → restauração em **~1 h**; paridade **100%** (contagem das 11 tabelas, índices, `reloptions` e md5 de amostras por faixa de PK idênticos) |
| 3 · Virada | app em `vigilo_app` (não-superuser), **TLS 1.3**; grafo ~10 ms, OSINT por endereço ~25 ms (cache quente — primeiro acesso a frio leva segundos; índices pré-carregados com `pg_prewarm`); re-coleta pela fila ok; **0 alertas falsos**, 0 jobs falhos |
| 4 · Reimport | `reimport-mensal.sh` rodou o pipeline **na VM** ("All files already processed"), `maintenance_run` #7 ok; cron semanal reativado |
| 5 · Disco | banco antigo parado, app conferido sem ele, volume apagado: VPS **86% → 25%** (73 GB livres) |

Achado na virada: `CREATE INDEX IF NOT EXISTS` exige `CREATE` no schema mesmo
quando o índice existe — o `papel-app.sh` concede `USAGE, CREATE ON SCHEMA
public` ao `vigilo_app` (continua sem superuser).

---

## Por quê e quanto ganha

- Disco do VPS em **84%** (16 GB livres de 96) em 22/10/2026. O volume
  `vigilo_cnpj-pgdata` ocupa **63 GB** e cresce a cada dump da Receita (o
  upsert do pipeline reescreve todas as linhas — ver runbook V2-F1 §7).
- Depois da migração o VPS cai para **~20%** de uso.
- Na Oracle: VM ARM com 4 núcleos, 24 GB de RAM e 200 GB de disco, de graça.
  A base (58 GB) + crescimento por dump + arquivos baixados cabem com folga.

## Fatos medidos (2026-10-22, no VPS de produção)

| Item | Valor | Consequência |
|---|---|---|
| Latência VPS → Oracle **São Paulo** (`sa-saopaulo-1`) | **1 ms** | O VPS está em SP: escolher São Paulo como região |
| Latência VPS → Vinhedo / Santiago / Ashburn | 8 / 49 / 124 ms | Só São Paulo serve sem penalidade |
| Consultas por tela do grafo (amostra de 5 empresas) | ~4 | +~5 ms por tela — desprezível |
| Imagem do pipeline `caiopizzol/cnpj-data-pipeline` | multi-arch (amd64 **+ arm64**) | Roda na VM ARM sem rebuild |
| PostgreSQL | 17.11 (`postgres:17-alpine`), collation `en_US.utf8` (libc) | Usar a **mesma imagem** na VM (mesma ordenação de texto nos índices) |
| Tamanho | 40 GB de tabelas + 18 GB de índices | Dump comprimido estimado em 10–15 GB |
| Reimport semanal no VPS (19/10) | 7h34 (2 vCPU) | Na VM (4 núcleos) tende a ser mais rápido |
| Roles / extensões | só `cnpj` / só `plpgsql` | Nada especial a recriar |

## Arquitetura depois da migração

```
VPS Hostinger (São Paulo)                    Oracle Cloud (sa-saopaulo-1)
┌──────────────────────────────┐             ┌───────────────────────────────────┐
│ app · queue · scheduler      │── TLS ─────▶│ postgres:17-alpine (rede do host) │
│   conexão `cnpj`             │  5432       │   /data/pgdata (block volume)     │
│   sslmode=verify-full        │  só do IP   │                                   │
│                              │  do VPS     │ ~/cnpj/bin/reimport.sh            │
│ cron semanal reimport-mensal │── SSH ─────▶│   roda o pipeline da Receita      │
│   (orquestra e grava o card) │             │   localmente (127.0.0.1)          │
└──────────────────────────────┘             └───────────────────────────────────┘
```

- O **pipeline da Receita passa a rodar na VM** (baixa e grava localmente). O
  VPS continua orquestrando: o `reimport-mensal.sh` dispara o pipeline por SSH,
  conta `processed_files`, e se veio dump novo roda re-coleta + normalização
  aqui e grava o `maintenance_run` (o card do `/admin` não muda).
- Sem mudança de produto: grafo, OSINT, refresh e dossiê só trocam o host.

## Segurança

O Postgres passa a ficar exposto na internet, então a porta 5432 é fechada em
**três camadas**, todas liberando **só o IP do VPS** (`148.230.77.119`):

1. **Security List** da Oracle (firewall da nuvem);
2. **iptables** da VM (a imagem Ubuntu da Oracle já vem com REJECT em tudo
   exceto SSH — o Postgres roda com rede do host justamente para essas regras
   valerem; porta publicada pelo Docker passaria por fora delas);
3. **pg_hba.conf**: só `hostssl` do IP do VPS, com `scram-sha-256`, e só para
   o papel `vigilo_app`.

E mais:

- **App sem superuser**: pela rede só entra o papel `vigilo_app`
  (`bin/papel-app.sh`) — NÃO-superuser, dono das tabelas (o app lê; a
  normalização de sócios faz UPDATE/DDL em `socios`, o que exige ser dono). O
  superuser `cnpj` fica só no socket e no `127.0.0.1` da VM (pipeline,
  manutenção): nada de `COPY ... TO PROGRAM` vindo da rede.
- **TLS verificado**: CA própria gerada na VM; o app conecta com
  `sslmode=verify-full` (criptografa e confere que está falando com o servidor
  certo — `prefer`/`require` não conferem). O certificado do servidor vence em
  **out/2031** (a CA em 2036): renovar com o `provisionar-vm.sh` depois de
  apagar `/data/certs/server.crt` (a `ca.key` fica na VM, só root, para isso).
- **Senhas novas e fortes** geradas na VM (`CNPJ_DB_PASSWORD` do superuser e
  `CNPJ_APP_PASSWORD` do app, em `~/cnpj/.env`, modo 600). A do app vai da VM
  para o `src/.env` do VPS por pipe direto — não passa pelo chat nem aparece
  na tela.
- **Acesso SSH à VM só por chave.** Você coloca a chave **pública** deste PC na
  criação da VM. **Nunca** cole senha do console da Oracle, chave privada ou
  senha de banco no chat. Eu não entro no console da Oracle.
- O VPS ganha uma chave SSH **dedicada** (`~/.ssh/cnpj_db`) só para o reimport e
  a cópia inicial.

---

## Fase 0 — Conta e VM [você] (~30 min)

1. **Criar a conta** em oracle.com/cloud/free. Na escolha da **Home Region**,
   selecione **Brazil East (São Paulo)**. ⚠️ Não dá para trocar depois, e os
   recursos grátis só existem na home region.
2. **(Recomendado) Converter para Pay As You Go** (Billing → Upgrade). Continua
   grátis dentro dos limites Always Free e:
   - evita a **recuperação por ociosidade** (a Oracle pode retomar VM grátis
     que passe 7 dias com CPU, rede e memória abaixo de 20%);
   - segundo relatos, facilita conseguir capacidade de VM ARM.

   Em seguida crie um **Budget** (Billing → Budgets) com alerta em US$ 1 — se
   algo sair do grátis, você fica sabendo na hora.
3. **Criar a VM** (Compute → Instances → Create instance):
   - **Image**: Canonical Ubuntu 24.04 (a versão *aarch64*);
   - **Shape**: Ampere → `VM.Standard.A1.Flex` com **4 OCPU e 24 GB**;
   - **Networking**: VCN/subnet padrão (pública), "Assign a public IPv4
     address" marcado;
   - **SSH keys**: "Paste public keys" → cole o conteúdo de
     `C:\Users\lukin\.ssh\id_ed25519.pub` (o arquivo **.pub**, que é público);
   - **Boot volume**: 50 GB.

   Se aparecer **"Out of capacity"**: tente de novo mais tarde (é comum em SP)
   — com Pay As You Go costuma sair mais rápido.
4. **Criar o disco de dados** (Storage → Block Volumes → Create): **150 GB**,
   mesmo Availability Domain da VM. Depois, na VM → Attached block volumes →
   Attach → tipo **Paravirtualized**, leitura/escrita.
   (50 + 150 = 200 GB, o limite grátis. Disco separado sobrevive se a VM
   precisar ser recriada.)
5. **Liberar o Postgres para o VPS** (Networking → VCN → Security List da
   subnet → Add Ingress Rule): origem `148.230.77.119/32`, TCP, porta `5432`.
   Mantenha a regra de SSH (22) que já vem.
6. **Me mande**: o **IP público** da VM. (O usuário é `ubuntu`.) Só isso.

## Fase 1 — Preparar a VM [Claude] (~1 h) — ✅ feita em 2026-10-23

VM `cnpj-db`, IP público `159.112.186.240`, Ubuntu 24.04 aarch64, 4 OCPU /
24 GB, boot 50 GB + block volume 150 GB. Tudo é feito pelo
`deploy/cnpj-db/provisionar-vm.sh` (idempotente — não reformata disco nem troca
senha/certificado se rodar de novo):

```bash
# do repo, copiar deploy/cnpj-db para ~/cnpj na VM e rodar:
sudo bash ~/cnpj/provisionar-vm.sh 148.230.77.119 159.112.186.240
```

1. Atualizar o sistema; instalar Docker (`docker.io` + `docker-compose-v2`);
   `unattended-upgrades` já vem ligado (patches de segurança).
2. Formatar o block volume (ext4, só se estiver vazio) e montar em `/data`
   (fstab por UUID, `nofail`). Pastas: `/data/pgdata`, `/data/dump`,
   `/data/certs`. O Docker guarda tudo em `/data/docker` (o download da Receita
   cai no disco grande) e **só sobe com `/data` montado**
   (`RequiresMountsFor`) — sem isso, um boot com o disco ausente subiria um
   Postgres vazio no disco do sistema. Logs dos containers com rotação.
3. **iptables**: ACCEPT `tcp/5432` de `148.230.77.119` antes do REJECT da
   imagem, aplicado na hora e escrito no `/etc/iptables/rules.v4` (sem
   `netfilter-persistent save`, que gravaria junto as chains do Docker).
4. **TLS**: gerar CA + certificado do servidor com o IP público no SAN; chave
   com dono `70:70` (usuário postgres da imagem alpine) e modo 600.
5. **Postgres** em `~/cnpj/docker-compose.yml`: `postgres:17-alpine`,
   `network_mode: host`, `shm_size: 1g`, dados em `/data/pgdata`, senha em
   `~/cnpj/.env` (gerada com `openssl rand`, modo 600). Config inicial para 24 GB:

   ```
   listen_addresses = '0.0.0.0'    ssl = on (TLSv1.2+)
   password_encryption = scram-sha-256
   shared_buffers = 6GB            effective_cache_size = 16GB
   work_mem = 32MB                 maintenance_work_mem = 1GB
   max_wal_size = 8GB              random_page_cost = 1.1
   log_connections = on
   ```

   `pg_hba.conf`: socket local (dentro do container) + `127.0.0.1` com senha
   (pipeline) + `hostssl cnpj vigilo_app 148.230.77.119/32 scram-sha-256`.
   Nada mais. Desligamento com prazo de 300 s (`stop_grace_period`,
   `shutdown-timeout` do Docker e `TimeoutStopSec` do systemd) para o
   checkpoint final não virar crash recovery.
6. Scripts em `~/cnpj/bin/`: `restaurar-dump.sh` (Fase 2 e recuperação
   rápida), `papel-app.sh` (papel `vigilo_app` dono das tabelas),
   `reimport.sh` (roda o pipeline contra
   `127.0.0.1`, com **checagem de espaço**: aborta se `/data` tiver < 25 GB
   livres; container nomeado e destacado — se o SSH do VPS cair no meio, o
   import continua e o retry só espera por ele), `pf-count.sh` (conta
   `processed_files`) e `gateway.sh` (comando forçado da chave do VPS: ela só
   pode pedir `reimport`, `pf-count` e — só durante a migração —
   `receive-dump`; nada de shell). Os arquivos da VM ficam versionados no repo
   em `deploy/cnpj-db/` — a VM é reproduzível.
7. Teste de reboot: disco, Docker, Postgres e regra de firewall voltam sozinhos.

## Fase 2 — Copiar a base [Claude] (~3–5 h, pode rodar de noite) — ✅ 2026-10-23

O site **não para**: o `pg_dump` lê um snapshot consistente enquanto o app usa
a base normalmente.

1. **[ok seu]** Pausar a linha do reimport no crontab do VPS (`0 3 * * 1 ...`)
   — nada escreve na base durante a cópia. Não rodar em segunda de madrugada.
2. **[ok seu]** Criar no VPS a chave dedicada `~/.ssh/cnpj_db` e autorizá-la na
   VM com comando forçado:

   ```
   restrict,command="/home/ubuntu/cnpj/bin/gateway.sh" ssh-ed25519 AAAA... vigilo-vps-cnpj
   ```
3. Dump direto do VPS para o disco da VM (sem ocupar disco no VPS), em
   background para sobreviver a queda de SSH:

   ```bash
   docker exec data-postgres-1 pg_dump -U cnpj -d cnpj -Fc -Z zstd:3 \
     | ssh -i ~/.ssh/cnpj_db ubuntu@159.112.186.240 receive-dump
   ```

   (Em 2026-10-23: 15 min, 7,6 GB — zstd gasta bem menos CPU que gzip nos
   2 vCPU do VPS.)

   O dump leva tudo: tabelas, `processed_files` (o pipeline sabe que o dump de
   2026-10 já está carregado e **não** recomeça do zero), a coluna e o índice
   `nome_norm`, os índices `cep`/`documento` e o ajuste de autovacuum.
4. Restaurar na VM em paralelo, refazer as estatísticas (o PG 17 não as leva
   no dump) e passar as tabelas para o papel `vigilo_app` — tudo no
   `bin/restaurar-dump.sh` (recusa base que já tenha tabelas):

   ```bash
   nohup ~/cnpj/bin/restaurar-dump.sh > ~/restaurar-dump.log 2>&1 &
   ```

5. **Conferência de paridade** VPS × VM — tem que bater 100%:
   - contagem de linhas de `empresas`, `estabelecimentos`, `socios`,
     `dados_simples`, `processed_files`;
   - lista de índices (sem `uf`/`situacao`/`cnae`; com `cep`, `documento`,
     `nome_norm`) e `reloptions` (`autovacuum_vacuum_scale_factor=0.02`);
   - `EXPLAIN` das consultas reais (coleta, grafo, filiais, OSINT por endereço
     e por sócio) usando os mesmos índices;
   - teste do VPS para a VM com `sslmode=verify-full` (psql em container
     efêmero) e latência das consultas.

## Fase 3 — Virada [Claude, com ok seu] (~30 min, sem parar o site) — ✅ 2026-10-23

**Código (fatia normal: gates + Juiz + commit), compatível com o modo atual** —
sem as variáveis novas, tudo continua como hoje:

- `config/database.php` (conexão `cnpj`): `sslrootcert` por env e
  `PDO::ATTR_TIMEOUT` (connect timeout, padrão 5 s, piso 1 s — hoje o padrão do
  driver é 30 s; com a base remota fora do ar, a tela não pode ficar pendurada).
- `deploy/reimport-mensal.sh`: com `CNPJ_REMOTE_SSH` definido, roda o pipeline
  e a contagem de `processed_files` na VM via SSH (`reimport.sh` /
  `pf-count.sh`); retry, card do `/admin`, re-coleta e normalização seguem
  iguais. Sem a variável, comportamento atual. **Baseline persistido**
  (`~/apps/vigilo/.cnpj-pf-baseline`): se o VPS cair no meio de um reimport e a
  VM terminar sozinha, a rodada seguinte ainda vê o dump novo e faz a
  re-coleta. VM sem espaço (saída 3) não é re-tentada. Testes:
  `bash tests/deploy/reimport-mensal-remote.test.sh` (Linux; `ssh`/`docker`
  falsos).
- `deploy/cnpj-db/`: compose, `postgresql.conf`, `pg_hba.conf` e scripts da VM.

**Virada:**

1. Deploy do código (`bash src/deploy/deploy.sh`).
2. Backup do env atual: `cp src/.env src/.env.bak-pre-oci`.
3. Copiar o certificado da CA para
   `src/storage/app/private/cnpj-db-ca.crt` (dentro do container:
   `/var/www/storage/app/private/cnpj-db-ca.crt`; não é segredo).
4. `src/.env`: `CNPJ_DB_HOST=<IP_VM>`, `CNPJ_DB_USERNAME=vigilo_app`,
   `CNPJ_DB_PASSWORD=<CNPJ_APP_PASSWORD da VM, via pipe>`,
   `CNPJ_DB_SSLMODE=verify-full`,
   `CNPJ_DB_SSLROOTCERT=/var/www/storage/app/private/cnpj-db-ca.crt`,
   `CNPJ_DB_CONNECT_TIMEOUT=5`.
   `~/apps/vigilo/.env`: `CNPJ_REMOTE_SSH=ubuntu@<IP_VM>`.
5. `config:cache` + `docker compose restart app queue scheduler nginx`
   (o worker da fila segura a conexão antiga até reiniciar).
6. **Smoke test**: `cnpj:status`; uma empresa da carteira re-coletada; grafo +
   beneficiários + filiais via tinker; OSINT por endereço e por sócio via
   `EmpresaLookup`; log do Postgres na VM mostrando as conexões do VPS **com
   SSL**.

**Rollback (instantâneo):** `cp src/.env.bak-pre-oci src/.env` +
`config:cache` + restart. A base local continua intacta até a Fase 5.

## Fase 4 — 1º reimport pela VM [Claude] (na segunda seguinte) — ✅ 2026-10-23 (rodada manual)

1. **[ok seu]** Reativar a linha do cron no VPS.
2. Rodar o `reimport-mensal.sh` uma vez na mão (sem dump novo, é um no-op de
   minutos) e conferir: pipeline executou **na VM**, contagem de
   `processed_files` veio pela VM, `maintenance_run` gravado no `/admin`.
3. Quando sair o dump de novembro: acompanhar o crescimento de `/data` e a
   duração do reimport na VM.

## Fase 5 — Liberar o disco do VPS [com ok seu — irreversível] (após ~7 dias estável) — ✅ 2026-10-23 (autorizado a fazer no mesmo dia)

1. `docker compose stop postgres` e esperar 1–2 dias sem nenhuma reclamação
   (prova de que nada mais usa a base local).
2. **[ok seu]** `docker compose rm postgres` +
   `docker volume rm vigilo_cnpj-pgdata` → libera **~63 GB**.
3. Tirar o serviço `postgres` e o volume `cnpj-pgdata` de
   `deploy/docker-compose.yml` (commit) e sincronizar a cópia de ops
   (`cp src/deploy/docker-compose.yml docker-compose.yml`).
4. Atualizar o runbook V2-F1 (§0, §2, §6, §7) para o modelo novo.
5. Tirar o verbo `receive-dump` do `gateway.sh` (só servia para a cópia).

O `cnpj.dump` da migração fica em `/data/dump` na VM como **restauração
rápida** (a base também é reconstruível do dump público da Receita, mas isso
leva horas + a normalização de sócios).

---

## Riscos e mitigação

| Risco | Mitigação |
|---|---|
| Oracle recuperar a VM por ociosidade | Pay As You Go (Fase 0.2). O `shared_buffers` de 6 GB também mantém a memória acima de 20% |
| "Out of capacity" ao criar a VM ARM | Tentar em outros horários; Pay As You Go. **Não** trocar de região (só SP tem 1 ms) |
| Cobrança acidental | Usar só shapes/volumes Always Free (A1 ≤ 4 OCPU/24 GB, ≤ 200 GB); Budget com alerta em US$ 1 |
| VM fora do ar | O app já degrada: grafo "indisponível", OSINT 503, refresh re-tenta na fila. Connect timeout de 5 s evita tela pendurada. Rollback = voltar o `.env` (até a Fase 5) |
| Exposição do Postgres na internet | 3 camadas restritas ao IP do VPS + TLS `verify-full` + `scram-sha-256` + senha forte nova; `log_connections` registra tentativas |
| IP público da VM mudar | O IP efêmero só muda se a VM for recriada; se acontecer, trocar o `CNPJ_DB_HOST`, refazer o certificado (o SAN tem o IP) e a regra do iptables |
| Perder a VM | Dados no block volume separado (sobrevive à VM) + `cnpj.dump` + base reconstruível do dump público |

## Checklist de aceite

- [ ] Paridade VPS × VM: contagens, índices e `reloptions` idênticos.
- [ ] App conectando com `verify-full`; log da VM mostra SSL nas conexões.
- [ ] Grafo, beneficiários, filiais, OSINT e refresh funcionando em produção.
- [ ] 1º reimport pela VM gravou `maintenance_run` com sucesso.
- [ ] Disco do VPS ~20% após a Fase 5.
