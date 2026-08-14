#!/usr/bin/env bash
#
# install-jitsi.sh — Instalação idempotente do Jitsi Meet (servidor "Core")
# ---------------------------------------------------------------------------
# Alvo: Ubuntu 24.04 LTS (Noble). Instala Jitsi Meet + Prosody + Jicofo + JVB,
# habilita TLS (Let's Encrypt) e autenticação por token (JWT / secure domain).
#
# Rode como root NA VM do Jitsi Core (não no servidor do portal).
#
#   # coloque antes os certificados em /opt/certificados/{fullchain.pem,cert.key}
#   sudo DOMAIN=meet.ciscopar.com.br ./install-jitsi.sh
#
# Variáveis (env ou jitsi.conf ao lado do script):
#   DOMAIN           (obrigatório)  FQDN do Jitsi, ex.: meet.ciscopar.com.br
#   CERT_DIR         (opcional)     Pasta dos certificados. Padrão: /opt/certificados
#   CERT_FULLCHAIN   (opcional)     Cert + cadeia (PEM). Padrão: <CERT_DIR>/fullchain.pem
#   CERT_KEY         (opcional)     Chave privada (PEM). Padrão: <CERT_DIR>/cert.key
#   LE_EMAIL         (opcional)     Alternativa: Let's Encrypt (só se NÃO houver cert próprio).
#   JWT_APP_ID       (opcional)     App id do token. Padrão: ciscopar
#   JWT_APP_SECRET   (opcional)     Segredo do token. Vazio = gera um e mostra no fim.
#   JVB_LOCAL_IP     (opcional)     IP interno da VM (NAT harvester do videobridge).
#   JVB_PUBLIC_IP    (opcional)     IP público/NAT (necessário se atrás de NAT).
#   ENABLE_UFW       (opcional)     true = configura o firewall UFW. Padrão: false.
#
# Reexecutável: pode rodar de novo para reconfigurar/atualizar.
# ---------------------------------------------------------------------------
set -euo pipefail

# --- carrega jitsi.conf (opcional) -----------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ -f "$SCRIPT_DIR/jitsi.conf" ] && . "$SCRIPT_DIR/jitsi.conf"

# --- parâmetros ------------------------------------------------------------
DOMAIN="${DOMAIN:-}"
CERT_DIR="${CERT_DIR:-/opt/certificados}"
CERT_FULLCHAIN="${CERT_FULLCHAIN:-$CERT_DIR/fullchain.pem}"
CERT_KEY="${CERT_KEY:-$CERT_DIR/cert.key}"
LE_EMAIL="${LE_EMAIL:-}"
JWT_APP_ID="${JWT_APP_ID:-ciscopar}"
JWT_APP_SECRET="${JWT_APP_SECRET:-}"
JVB_LOCAL_IP="${JVB_LOCAL_IP:-}"
JVB_PUBLIC_IP="${JVB_PUBLIC_IP:-}"
ENABLE_UFW="${ENABLE_UFW:-false}"

# --- logging ---------------------------------------------------------------
c() { printf '\033[%sm' "$1"; }
log()  { printf '%s➜ %s%s\n' "$(c '1;34')" "$*" "$(c 0)"; }
ok()   { printf '%s✔ %s%s\n' "$(c '1;32')" "$*" "$(c 0)"; }
warn() { printf '%s! %s%s\n' "$(c '1;33')" "$*" "$(c 0)"; }
die()  { printf '%s✖ %s%s\n' "$(c '1;31')" "$*" "$(c 0)" >&2; exit 1; }

# --- validações ------------------------------------------------------------
[ "$(id -u)" -eq 0 ] || die "Rode como root (sudo)."
[ -n "$DOMAIN" ]     || die "Defina DOMAIN (ex.: DOMAIN=meet.ciscopar.com.br)."
command -v apt-get >/dev/null || die "Este script é para Ubuntu/Debian (apt)."

. /etc/os-release 2>/dev/null || true
[ "${VERSION_ID:-}" = "24.04" ] || warn "Testado no Ubuntu 24.04; detectado '${VERSION_ID:-desconhecido}'. Prosseguindo."

[ -n "$JWT_APP_SECRET" ] || { JWT_APP_SECRET="$(openssl rand -hex 32)"; GENERATED_SECRET=true; }

export DEBIAN_FRONTEND=noninteractive

log "Instalação do Jitsi Core em ${DOMAIN} (app id JWT: ${JWT_APP_ID})"

# ---------------------------------------------------------------------------
# 1. Hostname / hosts
# ---------------------------------------------------------------------------
log "Configurando hostname e /etc/hosts..."
hostnamectl set-hostname "$DOMAIN" 2>/dev/null || true
if ! grep -qE "^127\.0\.0\.1\s+.*${DOMAIN}(\s|$)" /etc/hosts; then
    printf '127.0.0.1 %s\n' "$DOMAIN" >> /etc/hosts
fi
ok "Hostname pronto."

# ---------------------------------------------------------------------------
# 2. Pacotes base
# ---------------------------------------------------------------------------
log "Instalando pré-requisitos..."
apt-get update -y
apt-get install -y curl gnupg2 nginx-full apt-transport-https lsb-release ca-certificates openssl
# jitsi-videobridge precisa de um provedor Java (JRE headless)
apt-get install -y default-jre-headless || apt-get install -y openjdk-17-jre-headless
ok "Pré-requisitos instalados."

# ---------------------------------------------------------------------------
# 3. Repositório APT do Jitsi
# ---------------------------------------------------------------------------
log "Configurando o repositório do Jitsi..."
install -d -m 0755 /usr/share/keyrings
if [ ! -s /usr/share/keyrings/jitsi-keyring.gpg ]; then
    curl -fsSL https://download.jitsi.org/jitsi-key.gpg.key \
        | gpg --dearmor -o /usr/share/keyrings/jitsi-keyring.gpg
fi
echo "deb [signed-by=/usr/share/keyrings/jitsi-keyring.gpg] https://download.jitsi.org stable/" \
    > /etc/apt/sources.list.d/jitsi-stable.list
apt-get update -y
ok "Repositório configurado."

# ---------------------------------------------------------------------------
# 4. Firewall (opcional)
# ---------------------------------------------------------------------------
if [ "$ENABLE_UFW" = true ]; then
    log "Configurando UFW..."
    apt-get install -y ufw
    ufw allow 22/tcp        # SSH
    ufw allow 80/tcp        # HTTP (Let's Encrypt)
    ufw allow 443/tcp       # HTTPS + fallback
    ufw allow 10000/udp     # Mídia (JVB)
    ufw allow 4443/tcp      # Mídia fallback (JVB)
    ufw --force enable
    ok "UFW ativo."
else
    warn "UFW não configurado (ENABLE_UFW != true). Garanta no firewall: TCP 443/4443 e UDP 10000."
fi

# ---------------------------------------------------------------------------
# 5. Preseed (debconf) + instalação do Jitsi Meet
# ---------------------------------------------------------------------------
log "Instalando jitsi-meet..."
debconf-set-selections <<EOF
jitsi-videobridge2 jitsi-videobridge/jvb-hostname string ${DOMAIN}
jitsi-meet-web-config jitsi-meet/cert-choice select Generate a new self-signed certificate (You will later get a chance to obtain a Let's Encrypt certificate)
jitsi-meet-prosody jitsi-meet-prosody/jvb-hostname string ${DOMAIN}
EOF
apt-get install -y jitsi-meet
ok "jitsi-meet instalado."

# ---------------------------------------------------------------------------
# 6. TLS — certificado próprio (/opt/certificados) ou, alternativamente, LE
# ---------------------------------------------------------------------------
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}.conf"
if [ -f "$CERT_FULLCHAIN" ] && [ -f "$CERT_KEY" ]; then
    log "Aplicando certificado próprio ao nginx (${CERT_FULLCHAIN})..."
    [ -f "$NGINX_SITE" ] || die "Config nginx do Jitsi não encontrada: $NGINX_SITE"
    # substitui apenas as diretivas de certificado no site do Jitsi
    sed -i -E "s|^(\s*ssl_certificate_key)\s+.*;|\1 ${CERT_KEY};|" "$NGINX_SITE"
    sed -i -E "s|^(\s*ssl_certificate)\s+[^;]*;|\1 ${CERT_FULLCHAIN};|" "$NGINX_SITE"
    nginx -t || die "nginx -t falhou após apontar o certificado — confira $NGINX_SITE."
    ok "Certificado próprio aplicado."
elif [ -n "$LE_EMAIL" ]; then
    if [ ! -e "/etc/letsencrypt/live/${DOMAIN}" ]; then
        log "Sem cert em ${CERT_DIR}; tentando Let's Encrypt (requer DNS público + porta 80)..."
        echo "$LE_EMAIL" | /usr/share/jitsi-meet/scripts/install-letsencrypt-cert.sh \
            || die "Falha no Let's Encrypt. Para domínio interno, use o certificado próprio em ${CERT_DIR}."
        ok "Certificado emitido."
    else
        ok "Certificado Let's Encrypt já existe — mantido."
    fi
else
    warn "Sem certificado em ${CERT_DIR} (fullchain.pem/cert.key) e sem LE_EMAIL: seguindo self-signed."
    warn "Coloque os certificados em ${CERT_DIR} e rode o script novamente."
fi

# ---------------------------------------------------------------------------
# 7. Autenticação por token (JWT / secure domain)
# ---------------------------------------------------------------------------
log "Habilitando autenticação por token (JWT)..."
debconf-set-selections <<EOF
jitsi-meet-tokens jitsi-meet-tokens/appid string ${JWT_APP_ID}
jitsi-meet-tokens jitsi-meet-tokens/appsecret password ${JWT_APP_SECRET}
EOF
apt-get install -y jitsi-meet-tokens

# O token auth do Prosody depende do módulo Lua 'inspect'. O pacote lua-inspect do
# apt não cobre o Lua 5.4 do Prosody (só 5.1–5.3); inspect.lua é Lua puro, então
# garantimos uma cópia no caminho da versão de Lua usada pelo Prosody.
apt-get install -y lua-inspect || true
PLUA="$(prosodyctl about 2>/dev/null | grep -oiE 'Lua 5\.[0-9]' | grep -oE '5\.[0-9]' | head -1)"
PLUA="${PLUA:-5.4}"
if [ ! -f "/usr/share/lua/${PLUA}/inspect.lua" ]; then
    SRC_INSPECT="$(ls /usr/share/lua/*/inspect.lua 2>/dev/null | head -1)"
    if [ -n "$SRC_INSPECT" ]; then
        install -D -m 0644 "$SRC_INSPECT" "/usr/share/lua/${PLUA}/inspect.lua"
        ok "Módulo Lua 'inspect' disponibilizado para o Prosody (Lua ${PLUA})."
    else
        warn "lua-inspect não encontrado — o token auth pode falhar. Instale 'inspect.lua' manualmente."
    fi
fi
ok "JWT habilitado (só entra quem tiver token assinado pelo portal)."

# ---------------------------------------------------------------------------
# 8. NAT harvester do videobridge (on-premise atrás de NAT)
# ---------------------------------------------------------------------------
JVB_HOCON="/etc/jitsi/videobridge/jvb.conf"
if [ -n "$JVB_LOCAL_IP" ] && [ -n "$JVB_PUBLIC_IP" ] && [ -f "$JVB_HOCON" ]; then
    log "Configurando NAT harvester do JVB (local=${JVB_LOCAL_IP}, público=${JVB_PUBLIC_IP})..."
    if ! grep -q "nat-harvester" "$JVB_HOCON"; then
        # HOCON funde blocos de mesma chave; anexamos só o trecho de NAT.
        cat >> "$JVB_HOCON" <<EOF

// --- NAT harvester adicionado por install-jitsi.sh ---
videobridge {
    ice {
        nat-harvester {
            local-address = "${JVB_LOCAL_IP}"
            public-address = "${JVB_PUBLIC_IP}"
        }
    }
}
EOF
    fi
    ok "NAT harvester configurado."
else
    [ -n "$JVB_LOCAL_IP$JVB_PUBLIC_IP" ] && warn "Defina JVB_LOCAL_IP e JVB_PUBLIC_IP juntos para configurar NAT."
fi

# ---------------------------------------------------------------------------
# 9. Reinício dos serviços
# ---------------------------------------------------------------------------
log "Reiniciando serviços..."
systemctl restart prosody || true
systemctl restart jicofo || true
systemctl restart jitsi-videobridge2 || true
systemctl reload nginx || systemctl restart nginx || true
ok "Serviços reiniciados."

# ---------------------------------------------------------------------------
# 10. Resumo
# ---------------------------------------------------------------------------
echo
ok "Jitsi Core instalado em: https://${DOMAIN}"
echo "-----------------------------------------------------------------------"
echo " Credenciais do token (JWT) — guarde em local seguro e use no portal:"
echo "   JWT_APP_ID     = ${JWT_APP_ID}"
echo "   JWT_APP_SECRET = ${JWT_APP_SECRET}"
[ "${GENERATED_SECRET:-false}" = true ] && echo "   (segredo gerado automaticamente nesta execução)"
echo "-----------------------------------------------------------------------"
echo " Próximos passos:"
echo "   1. Abra https://${DOMAIN} e crie uma sala de teste (vai exigir token)."
echo "   2. Guarde JWT_APP_ID/JWT_APP_SECRET para o portal Laravel assinar os acessos."
echo "   3. Instale a VM de gravação com jitsi/install-jibri.sh."
echo "-----------------------------------------------------------------------"
