#!/usr/bin/env bash
#
# install-jibri.sh — Instala o Jibri (gravação/streaming) na VM dedicada
# ---------------------------------------------------------------------------
# Alvo: Ubuntu 24.04 LTS. Rode NA VM do Jibri (separada da Core), DEPOIS de ter
# rodado o enable-recording-core.sh no servidor Core.
#
#   sudo DOMAIN=meet.ciscopar.com.br \
#        JIBRI_CONTROL_PASS=... JIBRI_RECORDER_PASS=... ./install-jibri.sh
#
# As senhas precisam ser IGUAIS às usadas no enable-recording-core.sh.
#
# ⚠️ Jibri grava UMA reunião por vez. Para N gravações simultâneas, N VMs Jibri.
# ⚠️ Trecho mais frágil: casar versão do Chrome com o chromedriver. Ver notas.
# ---------------------------------------------------------------------------
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ -f "$SCRIPT_DIR/jitsi.conf" ] && . "$SCRIPT_DIR/jitsi.conf"

DOMAIN="${DOMAIN:-}"
JIBRI_CONTROL_PASS="${JIBRI_CONTROL_PASS:-}"
JIBRI_RECORDER_PASS="${JIBRI_RECORDER_PASS:-}"
RECORDINGS_DIR="${RECORDINGS_DIR:-/srv/recordings}"
CERT_DIR="${CERT_DIR:-/opt/certificados}"
CERT_FULLCHAIN="${CERT_FULLCHAIN:-$CERT_DIR/fullchain.pem}"
FINALIZE_SRC="${FINALIZE_SRC:-$SCRIPT_DIR/finalize.sh}"

c() { printf '\033[%sm' "$1"; }
log()  { printf '%s➜ %s%s\n' "$(c '1;34')" "$*" "$(c 0)"; }
ok()   { printf '%s✔ %s%s\n' "$(c '1;32')" "$*" "$(c 0)"; }
warn() { printf '%s! %s%s\n' "$(c '1;33')" "$*" "$(c 0)"; }
die()  { printf '%s✖ %s%s\n' "$(c '1;31')" "$*" "$(c 0)" >&2; exit 1; }

[ "$(id -u)" -eq 0 ]          || die "Rode como root (sudo)."
[ -n "$DOMAIN" ]              || die "Defina DOMAIN (FQDN do Core)."
[ -n "$JIBRI_CONTROL_PASS" ]  || die "Defina JIBRI_CONTROL_PASS."
[ -n "$JIBRI_RECORDER_PASS" ] || die "Defina JIBRI_RECORDER_PASS."
command -v apt-get >/dev/null || die "Este script é para Ubuntu/Debian (apt)."

export DEBIAN_FRONTEND=noninteractive
log "Instalando Jibri (grava para ${DOMAIN})"

# ---------------------------------------------------------------------------
# 1. Módulo de áudio ALSA loopback (obrigatório para o Jibri capturar som)
# ---------------------------------------------------------------------------
log "Configurando snd-aloop (ALSA loopback)..."
grep -qxF "snd-aloop" /etc/modules || echo "snd-aloop" >> /etc/modules
modprobe snd-aloop || warn "Não carregou snd-aloop agora (VM pode precisar de reboot com kernel completo)."
ok "snd-aloop configurado."

# ---------------------------------------------------------------------------
# 2. Pré-requisitos + Java + ffmpeg
# ---------------------------------------------------------------------------
log "Instalando pré-requisitos..."
apt-get update -y
apt-get install -y curl gnupg2 unzip ca-certificates ffmpeg alsa-utils jq
apt-get install -y default-jre-headless || apt-get install -y openjdk-17-jre-headless
ok "Pré-requisitos instalados."

# ---------------------------------------------------------------------------
# 3. Google Chrome + chromedriver (versões casadas)
# ---------------------------------------------------------------------------
log "Instalando Google Chrome..."
install -d -m 0755 /usr/share/keyrings
if [ ! -s /usr/share/keyrings/google-chrome.gpg ]; then
    curl -fsSL https://dl.google.com/linux/linux_signing_key.pub \
        | gpg --dearmor -o /usr/share/keyrings/google-chrome.gpg
fi
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-chrome.gpg] http://dl.google.com/linux/chrome/deb/ stable main" \
    > /etc/apt/sources.list.d/google-chrome.list
apt-get update -y
apt-get install -y google-chrome-stable
# Fixa a versão do Chrome (o Jibri é sensível a atualizações automáticas)
cat > /etc/default/google-chrome <<'EOF'
repo_add_once=false
EOF

CHROME_VER="$(google-chrome-stable --version | grep -oE '[0-9]+' | head -1)"
log "Chrome major = ${CHROME_VER}. Baixando chromedriver correspondente..."
CFT_JSON="$(curl -fsSL https://googlechromelabs.github.io/chrome-for-testing/known-good-versions-with-downloads.json)"
DRIVER_URL="$(echo "$CFT_JSON" | jq -r --arg v "$CHROME_VER" '
    [.versions[] | select(.version|startswith($v+"."))] | last
    | .downloads.chromedriver[]? | select(.platform=="linux64") | .url')"
if [ -n "$DRIVER_URL" ] && [ "$DRIVER_URL" != "null" ]; then
    tmp="$(mktemp -d)"; curl -fsSL "$DRIVER_URL" -o "$tmp/cd.zip"
    unzip -o -j "$tmp/cd.zip" -d /usr/local/bin "*/chromedriver" >/dev/null
    chmod +x /usr/local/bin/chromedriver; rm -rf "$tmp"
    ok "chromedriver $(chromedriver --version | awk '{print $2}') instalado."
else
    warn "Não achei chromedriver para o Chrome ${CHROME_VER}. Instale manualmente em /usr/local/bin/chromedriver."
fi

# ---------------------------------------------------------------------------
# 3b. Confiar no certificado interno (Chrome do Jibri acessa https://DOMAIN)
# ---------------------------------------------------------------------------
if [ -f "$CERT_FULLCHAIN" ]; then
    log "Instalando o certificado interno na trust store do sistema..."
    install -m 0644 "$CERT_FULLCHAIN" "/usr/local/share/ca-certificates/${DOMAIN}.crt"
    update-ca-certificates || warn "update-ca-certificates retornou aviso (verifique o formato PEM do cert)."
    ok "Certificado interno confiado."
else
    warn "Sem $CERT_FULLCHAIN — o Chrome do Jibri vai depender de --ignore-certificate-errors."
fi

# ---------------------------------------------------------------------------
# 4. Repositório do Jitsi + pacote jibri
# ---------------------------------------------------------------------------
log "Instalando o pacote jibri..."
if [ ! -s /usr/share/keyrings/jitsi-keyring.gpg ]; then
    curl -fsSL https://download.jitsi.org/jitsi-key.gpg.key \
        | gpg --dearmor -o /usr/share/keyrings/jitsi-keyring.gpg
fi
echo "deb [signed-by=/usr/share/keyrings/jitsi-keyring.gpg] https://download.jitsi.org stable/" \
    > /etc/apt/sources.list.d/jitsi-stable.list
apt-get update -y
apt-get install -y jibri
# O usuário 'jibri' precisa dos grupos de áudio/vídeo
usermod -aG adm,audio,video,plugdev jibri || true
ok "jibri instalado."

# ---------------------------------------------------------------------------
# 5. Diretório de gravações + finalize script
# ---------------------------------------------------------------------------
log "Preparando diretório de gravações e finalize..."
install -d -o jibri -g jibri "$RECORDINGS_DIR"
if [ -f "$FINALIZE_SRC" ]; then
    install -o jibri -g jibri -m 0755 "$FINALIZE_SRC" /etc/jitsi/jibri/finalize.sh
else
    warn "finalize.sh não encontrado em $FINALIZE_SRC — gravações ficarão só em $RECORDINGS_DIR."
    : > /etc/jitsi/jibri/finalize.sh; chmod +x /etc/jitsi/jibri/finalize.sh
fi
ok "Gravações em ${RECORDINGS_DIR}."

# ---------------------------------------------------------------------------
# 6. jibri.conf (aponta para o Core)
# ---------------------------------------------------------------------------
log "Escrevendo /etc/jitsi/jibri/jibri.conf..."
cat > /etc/jitsi/jibri/jibri.conf <<EOF
jibri {
  id = "jibri-ciscopar"
  single-use-mode = false
  api {
    http { external-api-port = 2222, internal-api-port = 3333 }
    xmpp { environments = [
      {
        name = "prod environment"
        xmpp-server-hosts = ["${DOMAIN}"]
        xmpp-domain = "${DOMAIN}"
        control-muc {
          domain = "internal.auth.${DOMAIN}"
          room-name = "JibriBrewery"
          nickname = "jibri-nickname"
        }
        control-login {
          domain = "auth.${DOMAIN}"
          username = "jibri"
          password = "${JIBRI_CONTROL_PASS}"
        }
        call-login {
          domain = "recorder.${DOMAIN}"
          username = "recorder"
          password = "${JIBRI_RECORDER_PASS}"
        }
        strip-from-room-domain = "conference."
        usage-timeout = 0 minutes
        trust-all-xmpp-certs = true
      }
    ] }
  }
  recording {
    recordings-directory = "${RECORDINGS_DIR}"
    finalize-script = "/etc/jitsi/jibri/finalize.sh"
  }
  chrome { flags = [
    "--use-fake-ui-for-media-stream", "--start-maximized", "--kiosk",
    "--enabled", "--disable-infobars", "--autoplay-policy=no-user-gesture-required",
    "--no-sandbox", "--disable-dev-shm-usage", "--ignore-certificate-errors"
  ] }
}
EOF
ok "jibri.conf escrito."

# ---------------------------------------------------------------------------
# 7. Serviços
# ---------------------------------------------------------------------------
log "Habilitando serviços do Jibri..."
systemctl daemon-reload
systemctl enable --now jibri || true
systemctl restart jibri || true
ok "Jibri iniciado."

echo
ok "Jibri instalado (grava para ${DOMAIN})."
echo "-----------------------------------------------------------------------"
echo " Verifique:  systemctl status jibri   e   /var/log/jitsi/jibri/log.0.txt"
echo " Teste:      entre em uma reunião no ${DOMAIN} e clique em 'Iniciar gravação'."
echo " Se o Chrome atualizar e quebrar, reinstale o chromedriver casado."
echo "-----------------------------------------------------------------------"
