#!/usr/bin/env bash
#
# Provisiona a VM Oracle da base CNPJ (Ubuntu 24.04 ARM, Always Free).
# Idempotente: rodar de novo não reformata o disco nem troca senha/certificado.
#
# Uso (na VM, com o conteúdo de deploy/cnpj-db copiado para ~/cnpj):
#   sudo bash ~/cnpj/provisionar-vm.sh <IP_DO_VPS> <IP_PUBLICO_DA_VM>
#
# Ver docs/vigilo-cnpj-oracle-migracao.md (Fase 1).
set -euo pipefail

VPS_IP="${1:?uso: provisionar-vm.sh <IP_DO_VPS> <IP_PUBLICO_DA_VM>}"
PUBLIC_IP="${2:?uso: provisionar-vm.sh <IP_DO_VPS> <IP_PUBLICO_DA_VM>}"
OWNER="${SUDO_USER:-ubuntu}"
DIR="$(getent passwd "$OWNER" | cut -d: -f6)/cnpj"
DEVICE=/dev/oracleoci/oraclevdb # block volume anexado como paravirtualized
PG_UID=70                       # usuário postgres da imagem alpine

[ "$(id -u)" = 0 ] || { echo "rode com sudo"; exit 1; }
[ -f "$DIR/docker-compose.yml" ] || { echo "copie deploy/cnpj-db para $DIR antes"; exit 1; }

export DEBIAN_FRONTEND=noninteractive

echo "==> sistema atualizado"
apt-get update -q
apt-get -y -q upgrade

echo "==> disco de dados em /data"
if ! findmnt -rn /data >/dev/null; then
    [ -b "$DEVICE" ] || { echo "block volume não encontrado em $DEVICE"; exit 1; }
    # Disco (ou partição dele) já montado = em uso (ex.: o disco do sistema).
    if lsblk -nro MOUNTPOINT "$DEVICE" | grep -q .; then
        echo "$DEVICE (ou uma partição dele) já está montado — abortando"; exit 1
    fi
    # Só formata disco VAZIO (sem sistema de arquivos).
    if [ -z "$(blkid -o value -s TYPE "$DEVICE" 2>/dev/null || true)" ]; then
        mkfs.ext4 -q -L cnpjdata "$DEVICE"
    fi
    UUID="$(blkid -o value -s UUID "$DEVICE")"
    mkdir -p /data
    grep -q "$UUID" /etc/fstab || echo "UUID=$UUID /data ext4 defaults,noatime,nofail 0 2" >>/etc/fstab
    mount /data
fi

echo "==> Docker (dados em /data, só sobe com /data montado)"
# Sem a dependência, um boot com o disco ausente subiria um Postgres VAZIO no
# disco do sistema. data-root em /data: imagens e arquivos temporários do
# pipeline (download da Receita) ficam no disco grande. Prazo de parada de 300 s
# (igual ao stop_grace_period do compose) para o Postgres desligar limpo.
DOCKER_CHANGED=0
install_if_changed() { # <destino> (conteúdo no stdin)
    local tmp
    tmp="$(mktemp)"
    cat >"$tmp"
    if ! cmp -s "$tmp" "$1"; then
        install -m 644 "$tmp" "$1"
        DOCKER_CHANGED=1
    fi
    rm -f "$tmp"
}
mkdir -p /etc/systemd/system/docker.service.d /etc/docker
install_if_changed /etc/systemd/system/docker.service.d/requer-data.conf <<'EOF'
[Unit]
RequiresMountsFor=/data

[Service]
TimeoutStopSec=330
EOF
install_if_changed /etc/docker/daemon.json <<'EOF'
{
  "data-root": "/data/docker",
  "shutdown-timeout": 300,
  "log-driver": "json-file",
  "log-opts": { "max-size": "20m", "max-file": "5" }
}
EOF
systemctl daemon-reload
apt-get -y -q install docker.io docker-compose-v2
systemctl enable --now docker
# Só reinicia quando a config mudou: re-rodar o script não derruba o Postgres
# nem um reimport em andamento.
if [ "$DOCKER_CHANGED" = 1 ]; then
    systemctl restart docker
fi
usermod -aG docker "$OWNER"

echo "==> firewall: 5432 só do VPS ($VPS_IP)"
RULE=(-s "$VPS_IP/32" -p tcp -m state --state NEW -m tcp --dport 5432 -j ACCEPT)
if ! iptables -C INPUT "${RULE[@]}" 2>/dev/null; then
    # Antes do REJECT final que a imagem da Oracle traz na chain INPUT.
    pos="$(iptables -L INPUT --line-numbers -n | awk '$2 == "REJECT" { print $1; exit }')"
    iptables -I INPUT "${pos:-1}" "${RULE[@]}"
fi
# Persistência no boot: edita o rules.v4 da imagem (não usa `netfilter-persistent
# save`, que gravaria junto as chains dinâmicas do Docker).
RULES_FILE=/etc/iptables/rules.v4
LINE="-A INPUT ${RULE[*]}"
if [ -f "$RULES_FILE" ] && ! grep -qF -- "$LINE" "$RULES_FILE"; then
    sed -i "0,/^-A INPUT -j REJECT/s||$LINE\n-A INPUT -j REJECT|" "$RULES_FILE"
fi

echo "==> pastas"
mkdir -p /data/pgdata /data/dump /data/certs /data/pgfn
chown "$PG_UID:$PG_UID" /data/pgdata
chown "$OWNER:$OWNER" /data/dump /data/pgfn # pgfn: zips da carga trimestral (bin/pgfn.sh)

echo "==> TLS (CA própria + certificado do servidor com o IP público no SAN)"
cd /data/certs
if [ ! -f ca.crt ]; then
    openssl req -x509 -newkey rsa:3072 -nodes -days 3650 \
        -subj "/CN=Vigilo CNPJ DB CA" -keyout ca.key -out ca.crt 2>/dev/null
fi
if [ ! -f server.crt ]; then
    openssl req -newkey rsa:3072 -nodes -subj "/CN=cnpj-db" \
        -keyout server.key -out server.csr 2>/dev/null
    openssl x509 -req -in server.csr -CA ca.crt -CAkey ca.key -CAcreateserial \
        -days 1825 -out server.crt 2>/dev/null \
        -extfile <(printf 'subjectAltName=IP:%s,DNS:cnpj-db\nkeyUsage=critical,digitalSignature,keyEncipherment\nextendedKeyUsage=serverAuth\n' "$PUBLIC_IP")
    rm -f server.csr
fi
chown root:root ca.key && chmod 600 ca.key
chown "$PG_UID:$PG_UID" server.key && chmod 600 server.key
chmod 644 ca.crt server.crt

echo "==> senhas do Postgres (geradas uma vez, nunca impressas)"
# CNPJ_DB_PASSWORD = superuser `cnpj` (só na VM); CNPJ_APP_PASSWORD = papel
# `vigilo_app` que o VPS usa (bin/papel-app.sh).
ENV_FILE="$DIR/.env"
for var in CNPJ_DB_PASSWORD CNPJ_APP_PASSWORD; do
    if ! grep -q "^${var}=" "$ENV_FILE" 2>/dev/null; then
        (umask 077 && echo "${var}=$(openssl rand -hex 24)" >>"$ENV_FILE")
    fi
done
chmod 600 "$ENV_FILE"

sed -i "s|VPS_IP/32|$VPS_IP/32|" "$DIR/pg_hba.conf"
chmod +x "$DIR"/bin/*.sh "$DIR"/provisionar-vm.sh
chown -R "$OWNER:$OWNER" "$DIR"

echo "==> Postgres"
cd "$DIR"
docker compose up -d
echo "==> pronto"
