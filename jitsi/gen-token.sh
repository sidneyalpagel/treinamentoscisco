#!/usr/bin/env bash
#
# gen-token.sh — Gera uma URL de teste do Jitsi com um token JWT válido (~1h).
# Lê JWT_APP_ID / JWT_APP_SECRET / DOMAIN do jitsi.conf ao lado.
#
# Uso:
#   ./gen-token.sh                      # sala "SalaTeste", usuário "Teste CISCOPAR"
#   ./gen-token.sh Reuniao42            # sala específica
#   ./gen-token.sh Reuniao42 "Fulano"   # sala + nome do usuário
# ---------------------------------------------------------------------------
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ -f "$SCRIPT_DIR/jitsi.conf" ] || { echo "jitsi.conf não encontrado em $SCRIPT_DIR" >&2; exit 1; }
. "$SCRIPT_DIR/jitsi.conf"

ROOM="${1:-SalaTeste}"
USER_NAME="${2:-Teste CISCOPAR}"

python3 - "$JWT_APP_ID" "$JWT_APP_SECRET" "$DOMAIN" "$ROOM" "$USER_NAME" <<'PY'
import sys, hmac, hashlib, base64, json, time
app_id, secret, domain, room, name = sys.argv[1:6]
b64 = lambda x: base64.urlsafe_b64encode(x).rstrip(b'=')
head = {"alg":"HS256","typ":"JWT"}
pl = {"aud":"jitsi","iss":app_id,"sub":domain,"room":room,"exp":int(time.time())+3600,
      "context":{"user":{"name":name,"email":"teste@ciscopar.com.br","moderator":True}}}
seg = b64(json.dumps(head,separators=(',',':')).encode())+b'.'+b64(json.dumps(pl,separators=(',',':')).encode())
sig = hmac.new(secret.encode(), seg, hashlib.sha256).digest()
print("https://%s/%s?jwt=%s" % (domain, room, (seg+b'.'+b64(sig)).decode()))
PY
