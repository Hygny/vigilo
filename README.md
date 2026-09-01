# Vigilo

**Due diligence contínua (KYB) de carteiras de CNPJ.**

Vigilo é um SaaS B2B onde uma organização sobe a lista de CNPJs com quem já se
relaciona (clientes, fornecedores, franqueados) e recebe **alertas** quando algo
muda no cadastro da Receita: situação cadastral (baixada/inapta/suspensa), troca
de sócio, mudança de endereço, CNAE ou porte.

Não é geração de leads. É monitoramento da carteira que o cliente **já tem**.

---

## ⚖️ Escopo legal (LGPD) — leia antes de estender

O dado da **pessoa jurídica** (razão social, situação, CNAE, endereço, QSA) é
público e de livre acesso. O dado do **sócio (pessoa física)** é dado pessoal e
protegido pela LGPD — aqui ele é tratado **exclusivamente** para monitorar a
carteira do próprio cliente (base legal: legítimo interesse em due diligence).

**Não-objetivos deliberados (não implementados de propósito):**

- ❌ **Não** exporta telefone/e-mail de sócios para marketing ou cold outreach.
- ❌ **Não** oferece busca aberta de "encontrar empresas por filtro" (isso é
  lead-gen e zona cinza de LGPD).

Se for evoluir o produto, mantenha essas duas linhas. Elas são o que separa
"KYB legítimo" de "vazamento de base".

---

## Stack

- **PHP 8.3+** (rodando em 8.4.24) · **Laravel 13**
- **MySQL** (padrão do Laragon)
- **Laravel Breeze** (stack Livewire 3 + Volt) para auth
- **Livewire 3 + Alpine + Tailwind** (sem SPA separada)
- Filas com driver **`database`**
- Testes **Pest 3** · Qualidade **Pint** + **Larastan (nível 8)**

---

## Setup no Laragon (Windows)

O Laragon já traz PHP, Composer, Node e MySQL. Ajuste os caminhos conforme sua
instalação (ex.: `C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64`).

```bash
# 1. Dependências
composer install
npm install

# 2. Ambiente
cp .env.example .env
php artisan key:generate

# 3. Banco (MySQL do Laragon: host 127.0.0.1, user root, senha vazia)
#    Crie o schema `vigilo` (Laragon > Database, ou):
#    mysql -u root -e "CREATE DATABASE vigilo"
php artisan migrate --seed

# 4. Assets
npm run build     # ou: npm run dev (para hot reload)

# 5. Suba a aplicação
php artisan serve            # http://localhost:8000
php artisan queue:work       # processa os jobs de refresh
php artisan schedule:work    # dispara o refresh mensal (dev)
```

**Usuário demo** (criado pelo seeder): `demo@vigilo.test` / `password`
— já vem com um portfólio de 5 CNPJs reais. Clique em **Atualizar** para popular
os dados via BrasilAPI.

### ⚠️ Certificado SSL para a BrasilAPI (Laragon)

O PHP do Laragon costuma vir **sem CA bundle configurado**, e a consulta HTTPS à
BrasilAPI falha com `cURL error 60: SSL certificate problem`. Aponte o `php.ini`
para o bundle que o próprio Laragon inclui:

```ini
; C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64\php.ini
curl.cainfo = "C:\laragon\etc\ssl\cacert.pem"
openssl.cafile = "C:\laragon\etc\ssl\cacert.pem"
```

Reinicie o Laragon depois. (O código trata o erro graciosamente — o job apenas
não cria snapshot e registra a falha — mas sem o bundle nenhuma consulta real
funciona.)

---

## Configuração (.env)

```dotenv
APP_NAME=Vigilo
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=vigilo
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

# Provedor de dados de CNPJ (fonte KYB)
CNPJ_DRIVER=brasilapi
CNPJ_BRASILAPI_URL=https://brasilapi.com.br/api/cnpj/v1
CNPJ_TIMEOUT=10
CNPJ_TRIES=3                  # tentativas em 429/5xx/erro de conexão
CNPJ_RETRY_BACKOFF_MS=500
CNPJ_THROTTLE_PER_MINUTE=20   # teto conservador de req/min (consumo da BrasilAPI)

# API pública de CNPJ (Vigilo servindo dados a integrações)
CNPJ_API_RATE_PER_MINUTE=30   # teto de req/min por token, GET /api/cnpj/{cnpj}
CNPJ_API_HISTORY_LIMIT=50     # máx. de mudanças em `historico` (mais novas primeiro)
```

O provider é trocável: `config/cnpj.php` define o driver e o
`App\Providers\AppServiceProvider` faz o bind de `App\Contracts\CnpjDataProvider`.
Para adicionar outra fonte, implemente o contrato e registre um novo `case`.

---

## API pública de CNPJ (para integrações)

Endpoint de leitura que serve os dados cadastrais que o Vigilo **já conhece** de
um CNPJ, para sistemas externos (ex.: automações de risco/anti-fraude). O Vigilo
é o **servidor**; o consumidor só precisa deste contrato:

```
GET {VIGILO_BASE_URL}/cnpj/{cnpj}
Authorization: Bearer {token}
Accept: application/json
```

- `{VIGILO_BASE_URL}` = origem do app **+ `/api`** — ex.: `https://vigilo.seudominio.com/api`.
- `{cnpj}` = 14 dígitos, **sem máscara**.
- Auth: token **Bearer (Sanctum)**. Cada token pertence a um usuário e herda a
  **organização** dele — a API só devolve CNPJs que **essa organização monitora**.
- Rate limit: `CNPJ_API_RATE_PER_MINUTE` (padrão **30/min**), por token.

**Emitir um token para um consumidor:**

```bash
php artisan vigilo:api-token usuario@empresa.com --name="fornecedor X"
```

O token é exibido **uma única vez** — copie na hora. Para revogar, remova a linha
correspondente em `personal_access_tokens` (coluna `name` identifica cada token).

**Resposta `200`:**

```json
{
  "cnpj": "12345678000190",
  "situacao_cadastral": "BAIXADA",
  "cnae_principal": "4713004",
  "razao_social": "COMERCIO XYZ LTDA",
  "historico": [
    { "data": "2025-02-10", "campo": "situacao_cadastral", "de": "ATIVA", "para": "BAIXADA" }
  ]
}
```

`historico` traz até `CNPJ_API_HISTORY_LIMIT` mudanças (padrão **50**), as **mais
recentes primeiro**.

**Status:**

| Código | Quando | O consumidor faz |
|---|---|---|
| `200` | CNPJ monitorado, com coleta | usa os dados |
| `401` | token ausente/inválido | corrige o token |
| `404` | CNPJ fora da carteira **ou** sem coleta ainda | trata como **pendente** (não trava) |
| `422` | CNPJ não tem 14 dígitos | corrige a entrada |
| `429` | acima do rate limit | espera e repete |

Os nomes de campo seguem o contrato acordado. O único ponto de ajuste, se o
consumidor esperar nomes diferentes, é o cliente dele (`VigiloClient`).

---

## Qualidade

```bash
vendor/bin/pint --test      # formatação (sem diffs)
vendor/bin/phpstan analyse  # análise estática (Larastan nível 8, 0 erros)
vendor/bin/pest             # suíte de testes
```

Os testes rodam em **SQLite em memória** (ver `phpunit.xml`); a aplicação usa
MySQL. É o split clássico: suíte rápida e isolada, app no banco real.

---

## Arquitetura (visão rápida)

```
app/
├─ Support/Cnpj.php              # Value object: normaliza + valida (mod-11)
├─ DTO/                          # CompanyData, PartnerData, ChangeEventData, ImportReport
├─ Contracts/CnpjDataProvider    # Interface do provedor
├─ Providers/Cnpj/BrasilApiProvider  # Http client: timeout, retry, throttle, mapeia QSA
├─ Services/
│  ├─ SnapshotService            # persiste snapshot + sócios (transação)
│  ├─ CompanyDiffer              # FUNÇÃO PURA: (snapshot anterior, novo) -> eventos
│  └─ CompanyImporter            # CSV -> normaliza/valida/dedupe -> relatório
├─ Jobs/
│  ├─ RefreshMonitoredCompanyJob # fetch -> snapshot -> diff -> eventos -> notifica
│  ├─ DispatchMonthlyRefreshJob  # Bus::batch de todos os monitorados
│  └─ ImportCompaniesCsvJob       # import assíncrono (arquivos grandes)
├─ Notifications/CompanyChangeDetected  # database + mail
└─ Livewire/                     # Dashboard, Portfolios, Companies, Alerts
```

O **coração do produto é o `CompanyDiffer`**: função pura, sem I/O, com bateria
de testes cobrindo cada tipo de mudança e sua severidade
(situação baixada = crítico, sócio = alto, endereço/CNAE/porte = médio, nome = baixo).

### Multi-tenant

`App\Models\Concerns\BelongsToOrganization` (global scope) isola portfólios por
organização do usuário logado; **policies** protegem portfólios e empresas.
Jobs e comandos de console rodam sem usuário autenticado e, portanto, enxergam
todos os tenants (necessário para o refresh do sistema).

### 🔭 Gancho v2 — grafo de sócios (NÃO implementado)

A tabela `company_partners` guarda cada vínculo com o `documento` **indexado**.
Essa é a matéria-prima para, no futuro, cruzar sócios entre empresas (grafo de
relacionamento societário). A travessia/consulta de grafo **não** está
implementada nesta versão — apenas a persistência bem-estruturada dos vínculos.
Ao construir a v2, respeite as restrições de LGPD acima: o grafo serve para
due diligence da carteira do cliente, não para prospecção.
