#!/usr/bin/env bash
#
# enable-recording-core.sh — Habilita gravação (Jibri) no lado do SERVIDOR CORE
# ---------------------------------------------------------------------------
# Rode na VM do Jitsi Core, DEPOIS do install-jitsi.sh. Prepara o Prosody, o
# jitsi-meet e o Jicofo para aceitarem um Jibri (contas de controle/gravação,
# domínio oculto do gravador e a "brewery" MUC).
#
#   sudo DOMAIN=meet.ciscopar.com.br \
#        JIBRI_CONTROL_PASS=... JIBRI_RECORDER_PASS=... ./enable-recording-core.sh
#
# As MESMAS senhas devem ir no install-jibri.sh (na VM Jibri). Use o jitsi.conf.
# ---------------------------------------------------------------------------
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ -f "$SCRIPT_DIR/jitsi.conf" ] && . "$SCRIPT_DIR/jitsi.conf"

DOMAIN="${DOMAIN:-}"
JIBRI_CONTROL_PASS="${JIBRI_CONTROL_PASS:-}"
JIBRI_RECORDER_PASS="${JIBRI_RECORDER_PASS:-}"

c() { printf '\033[%sm' "$1"; }
log()  { printf '%s➜ %s%s\n' "$(c '1;34')" "$*" "$(c 0)"; }
ok()   { printf '%s✔ %s%s\n' "$(c '1;32')" "$*" "$(c 0)"; }
warn() { printf '%s! %s%s\n' "$(c '1;33')" "$*" "$(c 0)"; }
die()  { printf '%s✖ %s%s\n' "$(c '1;31')" "$*" "$(c 0)" >&2; exit 1; }

[ "$(id -u)" -eq 0 ]          || die "Rode como root (sudo)."
[ -n "$DOMAIN" ]              || die "Defina DOMAIN."
[ -n "$JIBRI_CONTROL_PASS" ]  || die "Defina JIBRI_CONTROL_PASS."
[ -n "$JIBRI_RECORDER_PASS" ] || die "Defina JIBRI_RECORDER_PASS."
[ -f "/etc/prosody/prosody.cfg.lua" ] || die "Prosody não encontrado — rode o install-jitsi.sh antes."

PROSODY_CFG="/etc/prosody/conf.avail/${DOMAIN}.cfg.lua"
MEET_CFG="/etc/jitsi/meet/${DOMAIN}-config.js"
JICOFO_CFG="/etc/jitsi/jicofo/jicofo.conf"
[ -f "$PROSODY_CFG" ] || die "Config do Prosody ausente: $PROSODY_CFG"

# ---------------------------------------------------------------------------
# 1. Contas Prosody do Jibri (controle + gravador)
# ---------------------------------------------------------------------------
register_prosody() {   # user host pass  (idempotente: recria com a senha informada)
    prosodyctl deluser "${1}@${2}" 2>/dev/null || true
    prosodyctl register "$1" "$2" "$3"
}
log "Registrando contas do Jibri no Prosody..."
register_prosody "jibri"    "auth.${DOMAIN}"     "$JIBRI_CONTROL_PASS"
register_prosody "recorder" "recorder.${DOMAIN}" "$JIBRI_RECORDER_PASS"
ok "Contas jibri@auth.${DOMAIN} e recorder@recorder.${DOMAIN} criadas."

# ---------------------------------------------------------------------------
# 2. VirtualHost do gravador no Prosody
# ---------------------------------------------------------------------------
if ! grep -q "VirtualHost \"recorder.${DOMAIN}\"" "$PROSODY_CFG"; then
    log "Adicionando VirtualHost recorder.${DOMAIN}..."
    cat >> "$PROSODY_CFG" <<EOF

-- Gravador (Jibri) — adicionado por enable-recording-core.sh
VirtualHost "recorder.${DOMAIN}"
    modules_enabled = { "ping" }
    authentication = "internal_hashed"
EOF
    ok "VirtualHost do gravador adicionado."
else
    ok "VirtualHost do gravador já existe."
fi

# ---------------------------------------------------------------------------
# 3. jitsi-meet: liga gravação em arquivo + domínio oculto
# ---------------------------------------------------------------------------
if ! grep -q "enable-recording-core.sh" "$MEET_CFG"; then
    log "Ativando gravação em ${MEET_CFG}..."
    cat >> "$MEET_CFG" <<EOF

// Gravação (Jibri) — adicionado por enable-recording-core.sh
config.hiddenDomain = 'recorder.${DOMAIN}';
config.fileRecordingsEnabled = true;
config.liveStreamingEnabled = false;
config.recordingService = { enabled: true, sharingEnabled: true };
EOF
    ok "Gravação ativada no meet config."
else
    ok "Gravação já ativada no meet config."
fi

# ---------------------------------------------------------------------------
# 4. Jicofo: aponta para a brewery do Jibri
# ---------------------------------------------------------------------------
if ! grep -q "brewery-jid" "$JICOFO_CFG"; then
    log "Configurando a brewery do Jibri no Jicofo..."
    cat >> "$JICOFO_CFG" <<EOF

// Jibri — adicionado por enable-recording-core.sh
jicofo {
  jibri {
    brewery-jid = "JibriBrewery@internal.auth.${DOMAIN}"
    pending-timeout = 90 seconds
  }
}
EOF
    ok "Jicofo configurado."
else
    ok "Jicofo já aponta para a brewery."
fi

# ---------------------------------------------------------------------------
# 5. Reinício
# ---------------------------------------------------------------------------
log "Reiniciando Prosody e Jicofo..."
systemctl restart prosody
systemctl restart jicofo
ok "Serviços reiniciados."

echo
ok "Core preparado para gravação."
echo "  Agora rode o jitsi/install-jibri.sh na VM Jibri usando as MESMAS senhas."
