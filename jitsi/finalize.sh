#!/usr/bin/env bash
#
# finalize.sh — executado pelo Jibri ao terminar cada gravação.
# Recebe como $1 o diretório da gravação (contém o .mp4 e metadata.json).
#
# Opcional (defina em /etc/jitsi/jibri/finalize.env):
#   MINIO_ALIAS      alias do mc já configurado (ex.: "ciscopar")
#   MINIO_BUCKET     bucket de destino (ex.: "gravacoes")
#   PORTAL_WEBHOOK   URL do portal para registrar a gravação
#   PORTAL_TOKEN     token Bearer para o webhook
#
# Sem nenhuma dessas variáveis, apenas mantém o arquivo no diretório local.
# ---------------------------------------------------------------------------
set -euo pipefail

REC_DIR="${1:?uso: finalize.sh <dir-da-gravacao>}"
[ -f /etc/jitsi/jibri/finalize.env ] && . /etc/jitsi/jibri/finalize.env

log() { printf '[finalize] %s\n' "$*"; }

MP4="$(find "$REC_DIR" -maxdepth 1 -name '*.mp4' | head -1 || true)"
META="$REC_DIR/metadata.json"
[ -n "$MP4" ] || { log "nenhum .mp4 em $REC_DIR"; exit 0; }
log "gravação: $MP4"

# 1. Envia ao MinIO (S3) se configurado
OBJETO=""
if [ -n "${MINIO_ALIAS:-}" ] && [ -n "${MINIO_BUCKET:-}" ] && command -v mc >/dev/null; then
    OBJETO="$(basename "$MP4")"
    log "enviando ao MinIO: ${MINIO_ALIAS}/${MINIO_BUCKET}/${OBJETO}"
    mc cp "$MP4" "${MINIO_ALIAS}/${MINIO_BUCKET}/${OBJETO}" && log "upload ok" || log "falha no upload"
fi

# 2. Avisa o portal (para vincular a gravação ao treinamento/reunião)
if [ -n "${PORTAL_WEBHOOK:-}" ]; then
    SALA="$(jq -r '.meeting_url // .participants[0].email // empty' "$META" 2>/dev/null || true)"
    log "notificando o portal: $PORTAL_WEBHOOK"
    curl -fsS -X POST "$PORTAL_WEBHOOK" \
        -H "Content-Type: application/json" \
        ${PORTAL_TOKEN:+-H "Authorization: Bearer $PORTAL_TOKEN"} \
        -d "$(jq -n \
              --arg arquivo "$(basename "$MP4")" \
              --arg objeto "$OBJETO" \
              --arg sala "$SALA" \
              --arg bucket "${MINIO_BUCKET:-}" \
              '{arquivo:$arquivo, objeto:$objeto, sala:$sala, bucket:$bucket}')" \
        && log "portal notificado" || log "falha ao notificar o portal"
fi

log "concluído."
