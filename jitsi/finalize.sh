#!/usr/bin/env bash
#
# finalize.sh — executado pelo Jibri ao terminar cada gravação.
# Recebe como $1 o diretório da gravação (contém o .mp4 e metadata.json).
#
# Avisa o portal (webhook) que a gravação está pronta. O arquivo PERMANECE
# aqui no Jibri (servido pelo nginx do install-jibri.sh); o portal faz o
# streaming quando o gestor baixa.
#
# Configuração em /etc/jitsi/jibri/finalize.env (criado pelo install-jibri.sh):
#   PORTAL_WEBHOOK   URL do webhook do portal (ex.: https://treinamentos.ciscopar.com.br/webhooks/gravacao)
#   PORTAL_TOKEN     token compartilhado (= gravacao_secret do portal)
# ---------------------------------------------------------------------------
set -euo pipefail

REC_DIR="${1:?uso: finalize.sh <dir-da-gravacao>}"
[ -f /etc/jitsi/jibri/finalize.env ] && . /etc/jitsi/jibri/finalize.env

log() { printf '[finalize] %s\n' "$*"; }

MP4="$(find "$REC_DIR" -maxdepth 1 -name '*.mp4' | head -1 || true)"
META="$REC_DIR/metadata.json"
[ -n "$MP4" ] || { log "nenhum .mp4 em $REC_DIR"; exit 0; }
log "gravação: $MP4"

if [ -z "${PORTAL_WEBHOOK:-}" ] || [ -z "${PORTAL_TOKEN:-}" ]; then
    log "PORTAL_WEBHOOK/PORTAL_TOKEN não configurados — arquivo mantido, sem notificar o portal."
    exit 0
fi

# Sala = último segmento da meeting_url do metadata.json
SALA=""
if [ -f "$META" ]; then
    SALA="$(jq -r '.meeting_url // empty' "$META" 2>/dev/null | sed -e 's/[?].*$//' -e 's#/*$##' -e 's#.*/##')"
fi
[ -n "$SALA" ] || { log "não consegui extrair a sala do metadata.json — abortando notificação."; exit 0; }

# Caminho relativo (o nginx serve /srv/recordings): <dir-da-sessao>/<arquivo>.mp4
ARQUIVO="$(basename "$REC_DIR")/$(basename "$MP4")"
TAMANHO="$(stat -c%s "$MP4" 2>/dev/null || echo 0)"

log "notificando o portal: sala=$SALA arquivo=$ARQUIVO tamanho=$TAMANHO"
curl -fsS -X POST "$PORTAL_WEBHOOK" \
    -H "Authorization: Bearer $PORTAL_TOKEN" \
    -H "Content-Type: application/json" \
    -d "$(jq -n --arg sala "$SALA" --arg arq "$ARQUIVO" --argjson tam "${TAMANHO:-0}" \
          '{sala:$sala, arquivo:$arq, tamanho:$tam}')" \
    && log "portal notificado." || log "falha ao notificar o portal."

log "concluído."
