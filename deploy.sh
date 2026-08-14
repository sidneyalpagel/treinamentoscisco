#!/usr/bin/env bash
#
# deploy.sh — Implantação da Plataforma de Treinamentos em servidores HestiaCP
# ---------------------------------------------------------------------------
# Fluxo de uso:
#     git clone https://github.com/sidneyalpagel/treinamentoscisco.git laravel
#     cd laravel
#     sudo HESTIA_USER=ciscopar DOMAIN=treinamentos.ciscopar.com.br ./deploy.sh
#
# O script roda DENTRO do próprio repositório clonado e cuida de TUDO:
#   0. Instala as dependências que faltarem (composer e Node.js/npm)
#   1. Atualiza o código (git pull) em execuções seguintes
#   2. Cria e configura o .env (APP_KEY, banco, URL) na primeira execução
#   3. Instala dependências PHP (composer) e compila os assets (npm/vite)
#   4. Roda as migrations
#   5. Recria os caches do Laravel (config/route/view)
#   6. Ajusta dono e permissões (storage / bootstrap/cache)
#   7. Aponta o docroot do Hestia (public_html) para <repo>/public
#   8. Recarrega o PHP-FPM
#
# Pré-requisitos feitos UMA vez pelo painel Hestia (não pelo script):
#   - Usuário do painel + domínio web + versão do PHP do domínio
#   - Banco MySQL/MariaDB criado (anote nome, usuário e senha para o .env)
#
# Recomenda-se rodar como root (instala pacotes de sistema, faz chown e recarrega
# o PHP-FPM). Também roda como o usuário do domínio, mas aí os passos que exigem
# root serão apenas avisados.
# ---------------------------------------------------------------------------

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Configuração externa opcional (não versionada — veja deploy.conf.example)
if [ -f "$SCRIPT_DIR/deploy.conf" ]; then
    # shellcheck disable=SC1091
    source "$SCRIPT_DIR/deploy.conf"
fi

# ---------------------------------------------------------------------------
# Configuração (variáveis de ambiente ou deploy.conf sobrescrevem estes padrões)
# ---------------------------------------------------------------------------
HESTIA_USER="${HESTIA_USER:-}"                                  # usuário do painel Hestia (obrigatório)
DOMAIN="${DOMAIN:-}"                                            # domínio web no Hestia (obrigatório)
BRANCH="${BRANCH:-main}"
PHP_VERSION="${PHP_VERSION:-8.3}"
NODE_MAJOR="${NODE_MAJOR:-22}"                                  # versão do Node instalada se faltar

APP_DIR="${APP_DIR:-$SCRIPT_DIR}"                               # o próprio repositório clonado
DOMAIN_DIR="${DOMAIN_DIR:-/home/${HESTIA_USER}/web/${DOMAIN}}"
DOCROOT="${DOCROOT:-${DOMAIN_DIR}/public_html}"

# .env (usado só na PRIMEIRA execução, quando o .env ainda não existe)
APP_URL="${APP_URL:-https://${DOMAIN}}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-}"
DB_USERNAME="${DB_USERNAME:-}"
DB_PASSWORD="${DB_PASSWORD:-}"

# Passos (true/false)
INSTALL_SYSTEM_DEPS="${INSTALL_SYSTEM_DEPS:-true}"             # instala composer/node se faltarem
UPDATE_CODE="${UPDATE_CODE:-true}"                            # git pull nas execuções seguintes
SETUP_DOCROOT="${SETUP_DOCROOT:-true}"
BUILD_ASSETS="${BUILD_ASSETS:-true}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
RUN_SEED="${RUN_SEED:-false}"
MAINTENANCE_MODE="${MAINTENANCE_MODE:-true}"
RELOAD_FPM="${RELOAD_FPM:-true}"

# Binários
PHP_BIN="${PHP_BIN:-php${PHP_VERSION}}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

# ---------------------------------------------------------------------------
# Logging
# ---------------------------------------------------------------------------
if [ -t 1 ]; then
    c_reset='\033[0m'; c_blue='\033[1;34m'; c_green='\033[1;32m'; c_yellow='\033[1;33m'; c_red='\033[1;31m'
else
    c_reset=''; c_blue=''; c_green=''; c_yellow=''; c_red=''
fi
log()  { echo -e "${c_blue}➜${c_reset} $*"; }
ok()   { echo -e "${c_green}✓${c_reset} $*"; }
warn() { echo -e "${c_yellow}⚠${c_reset} $*"; }
die()  { echo -e "${c_red}✗ $*${c_reset}" >&2; exit 1; }

# ---------------------------------------------------------------------------
# Validações e helpers
# ---------------------------------------------------------------------------
[ -n "$HESTIA_USER" ] || die "Defina HESTIA_USER (usuário do painel Hestia)."
[ -n "$DOMAIN" ]      || die "Defina DOMAIN (domínio web)."
[ -f "$APP_DIR/artisan" ] || die "Rode o script na raiz do repositório clonado (artisan não encontrado em $APP_DIR)."

IS_ROOT=false
[ "$(id -u)" -eq 0 ] && IS_ROOT=true

# Executa um comando dentro de APP_DIR como o usuário do domínio (mantém o dono correto)
run_app() {
    if $IS_ROOT; then
        runuser -u "$HESTIA_USER" -- bash -lc "cd '$APP_DIR' && $*"
    else
        bash -lc "cd '$APP_DIR' && $*"
    fi
}

# Traz o app de volta caso algo falhe no meio
WENT_DOWN=false
cleanup() { if [ "$WENT_DOWN" = true ]; then run_app "$PHP_BIN artisan up" >/dev/null 2>&1 || true; fi; }
trap cleanup EXIT

# Atualiza/insere uma chave no .env
set_env() {
    local key="$1" val="$2" file="$APP_DIR/.env"
    local esc; esc=$(printf '%s' "$val" | sed -e 's/[&|\\]/\\&/g')
    if grep -qE "^#?\s*${key}=" "$file"; then
        sed -i -E "s|^#?\s*${key}=.*|${key}=${esc}|" "$file"
    else
        printf '%s=%s\n' "$key" "$val" >> "$file"
    fi
}

echo
log "Implantação: ${DOMAIN} (usuário Hestia: ${HESTIA_USER}, PHP ${PHP_VERSION})"
log "Repositório: ${APP_DIR}"
echo

# ---------------------------------------------------------------------------
# 0. Dependências de sistema (composer e Node.js/npm) — instala o que faltar
# ---------------------------------------------------------------------------
command -v "$PHP_BIN" >/dev/null 2>&1 || { command -v php >/dev/null 2>&1 && PHP_BIN="php"; }
command -v "$PHP_BIN" >/dev/null 2>&1 || die "PHP não encontrado. Instale o PHP ${PHP_VERSION} (via Hestia) e tente novamente."

if [ "$INSTALL_SYSTEM_DEPS" = true ]; then
    # Composer
    if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
        log "Composer não encontrado — instalando..."
        tmp="$(mktemp -d)"
        "$PHP_BIN" -r "copy('https://getcomposer.org/installer', '$tmp/composer-setup.php');"
        "$PHP_BIN" "$tmp/composer-setup.php" --install-dir="$tmp" --filename=composer
        if $IS_ROOT; then install -m 0755 "$tmp/composer" /usr/local/bin/composer; COMPOSER_BIN="composer"
        else COMPOSER_BIN="$tmp/composer"; warn "Composer instalado em $COMPOSER_BIN (rode como root para instalar em /usr/local/bin)."; fi
        rm -rf "$tmp/composer-setup.php"
        ok "Composer instalado."
    fi
    # Node.js / npm
    if ! command -v "$NPM_BIN" >/dev/null 2>&1; then
        if $IS_ROOT && command -v apt-get >/dev/null 2>&1; then
            log "Node.js não encontrado — instalando Node ${NODE_MAJOR}.x (NodeSource)..."
            curl -fsSL "https://deb.nodesource.com/setup_${NODE_MAJOR}.x" | bash -
            apt-get install -y nodejs
            ok "Node.js $(node -v) instalado."
        else
            die "Node.js/npm não encontrado. Instale o Node ${NODE_MAJOR}+ (ex.: NodeSource) ou defina BUILD_ASSETS=false."
        fi
    fi
fi

# ---------------------------------------------------------------------------
# Se for root, garante que o repositório pertence ao usuário do domínio
# ---------------------------------------------------------------------------
if $IS_ROOT; then
    chown -R "$HESTIA_USER":"$HESTIA_USER" "$APP_DIR"
fi

# ---------------------------------------------------------------------------
# 1. Atualiza o código (nas execuções seguintes)
# ---------------------------------------------------------------------------
if [ "$UPDATE_CODE" = true ] && [ -d "$APP_DIR/.git" ]; then
    log "Atualizando código para origin/${BRANCH}..."
    run_app "git fetch --prune origin && git checkout '$BRANCH' && git reset --hard 'origin/$BRANCH'"
    ok "Código atualizado."
fi

# ---------------------------------------------------------------------------
# 2. Dependências PHP (necessárias antes do artisan)
# ---------------------------------------------------------------------------
log "Instalando dependências PHP (produção)..."
run_app "$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction --prefer-dist"
ok "Dependências PHP instaladas."

# ---------------------------------------------------------------------------
# 3. .env (só na primeira execução)
# ---------------------------------------------------------------------------
if [ ! -f "$APP_DIR/.env" ]; then
    log "Primeira execução — criando e configurando o .env..."
    run_app "cp .env.example .env"

    # Pede as credenciais do banco se não vieram por variável/conf
    if [ -z "$DB_DATABASE" ] && [ -t 0 ]; then read -r -p "Nome do banco (DB_DATABASE): " DB_DATABASE; fi
    if [ -z "$DB_USERNAME" ] && [ -t 0 ]; then read -r -p "Usuário do banco (DB_USERNAME): " DB_USERNAME; fi
    if [ -z "$DB_PASSWORD" ] && [ -t 0 ]; then read -r -s -p "Senha do banco (DB_PASSWORD): " DB_PASSWORD; echo; fi

    set_env APP_NAME '"Plataforma de Treinamentos"'
    set_env APP_ENV production
    set_env APP_DEBUG false
    set_env APP_URL "$APP_URL"
    set_env APP_TIMEZONE America/Sao_Paulo
    set_env APP_LOCALE pt_BR
    set_env DB_CONNECTION mysql
    set_env DB_HOST "$DB_HOST"
    set_env DB_PORT "$DB_PORT"
    set_env DB_DATABASE "$DB_DATABASE"
    set_env DB_USERNAME "$DB_USERNAME"
    set_env DB_PASSWORD "\"$DB_PASSWORD\""

    run_app "$PHP_BIN artisan key:generate --force"
    $IS_ROOT && chown "$HESTIA_USER":"$HESTIA_USER" "$APP_DIR/.env"
    ok ".env configurado (APP_URL=${APP_URL}, banco=${DB_DATABASE})."
fi

# ---------------------------------------------------------------------------
# 4. Manutenção
# ---------------------------------------------------------------------------
if [ "$MAINTENANCE_MODE" = true ]; then
    run_app "$PHP_BIN artisan down --retry=15" || true
    WENT_DOWN=true
fi

# ---------------------------------------------------------------------------
# 5. Assets
# ---------------------------------------------------------------------------
if [ "$BUILD_ASSETS" = true ]; then
    log "Compilando assets (npm)..."
    run_app "if [ -f package-lock.json ]; then $NPM_BIN ci; else $NPM_BIN install; fi"
    run_app "$NPM_BIN run build"
    ok "Assets compilados."
fi

# ---------------------------------------------------------------------------
# 6. Migrations / seed
# ---------------------------------------------------------------------------
if [ "$RUN_MIGRATIONS" = true ]; then
    log "Rodando migrations..."
    run_app "$PHP_BIN artisan migrate --force"
    ok "Migrations aplicadas."
fi
if [ "$RUN_SEED" = true ]; then
    log "Rodando seeders..."
    run_app "$PHP_BIN artisan db:seed --force"
    ok "Seed concluído."
fi

# ---------------------------------------------------------------------------
# 7. Caches do Laravel
# ---------------------------------------------------------------------------
log "Recriando caches (config/route/view)..."
run_app "$PHP_BIN artisan storage:link" >/dev/null 2>&1 || true
run_app "$PHP_BIN artisan config:cache"
run_app "$PHP_BIN artisan route:cache"
run_app "$PHP_BIN artisan view:cache"
run_app "$PHP_BIN artisan event:cache" >/dev/null 2>&1 || true
ok "Caches atualizados."

# ---------------------------------------------------------------------------
# 8. Dono e permissões
# ---------------------------------------------------------------------------
if $IS_ROOT; then
    chown -R "$HESTIA_USER":"$HESTIA_USER" "$APP_DIR"
fi
chmod -R ug+rwX "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
ok "Permissões ajustadas (storage / bootstrap/cache)."

# ---------------------------------------------------------------------------
# 9. Docroot do Hestia -> <repo>/public
# ---------------------------------------------------------------------------
if [ "$SETUP_DOCROOT" = true ]; then
    target="$APP_DIR/public"
    if [ -L "$DOCROOT" ] && [ "$(readlink -f "$DOCROOT")" = "$(readlink -f "$target")" ]; then
        ok "Docroot já aponta para o public do app."
    else
        if [ -e "$DOCROOT" ] && [ ! -L "$DOCROOT" ]; then
            backup="${DOCROOT}.bak.$(date +%Y%m%d%H%M%S)"
            warn "public_html existente movido para ${backup}"
            mv "$DOCROOT" "$backup"
        else
            rm -f "$DOCROOT"
        fi
        ln -s "$target" "$DOCROOT"
        $IS_ROOT && chown -h "$HESTIA_USER":"$HESTIA_USER" "$DOCROOT"
        ok "public_html -> ${target}"
    fi
fi

# ---------------------------------------------------------------------------
# 10. Reload do PHP-FPM
# ---------------------------------------------------------------------------
if [ "$RELOAD_FPM" = true ]; then
    fpm_service="php${PHP_VERSION}-fpm"
    if $IS_ROOT; then
        systemctl reload "$fpm_service" 2>/dev/null && ok "PHP-FPM (${fpm_service}) recarregado." \
            || warn "Não foi possível recarregar ${fpm_service}. Recarregue manualmente."
    elif command -v sudo >/dev/null 2>&1 && sudo -n true 2>/dev/null; then
        sudo systemctl reload "$fpm_service" && ok "PHP-FPM recarregado." || warn "Falha ao recarregar ${fpm_service}."
    else
        warn "Recarregue o PHP-FPM manualmente: sudo systemctl reload ${fpm_service}"
    fi
fi

# ---------------------------------------------------------------------------
# 11. Sai da manutenção
# ---------------------------------------------------------------------------
if [ "$MAINTENANCE_MODE" = true ]; then
    run_app "$PHP_BIN artisan up"
    WENT_DOWN=false
fi

echo
ok "Implantação concluída — ${APP_URL}"
