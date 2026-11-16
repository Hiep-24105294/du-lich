#!/usr/bin/env bash
#
# Chạy TRÊN VPS dưới root, do GitHub Actions gọi qua SSH sau khi build image (deploy-vps.yml).
# Idempotent: lần đầu tự clone, sinh secret, cài nginx, xin chứng chỉ; các lần sau chỉ kéo
# image mới và khởi động lại. Chạy tay cũng được:
#   DISANSO_DOMAIN=disanso.180.93.2.63.nip.io bash deploy.sh
#
set -Eeuo pipefail

DOMAIN="${DISANSO_DOMAIN:?DISANSO_DOMAIN bắt buộc}"
IMAGE_TAG="${DISANSO_IMAGE_TAG:-latest}"
APP_DIR="${DISANSO_APP_DIR:-/srv/disanso}"
REPO_URL="${DISANSO_REPO_URL:-https://github.com/Hiep-24105294/du-lich.git}"
PORT="${DISANSO_PORT:-8080}"

SRC="$APP_DIR/02_SourceCode"
VPS="$SRC/deploy/vps"
ENV_FILE="$VPS/.env"
NGINX_CONF=/etc/nginx/sites-available/disanso.conf

log() { echo "[deploy] $(date '+%F %T') $*"; }

# 1. Mã nguồn: compose.yaml + lớp phủ VPS đọc từ đây; image thì kéo từ GHCR, không build.
if [[ ! -d "$APP_DIR/.git" ]]; then
  log "clone $REPO_URL → $APP_DIR"
  git clone --depth 1 "$REPO_URL" "$APP_DIR"
fi
git -C "$APP_DIR" fetch --depth 1 origin main
git -C "$APP_DIR" reset --hard origin/main --quiet
log "mã nguồn ở $(git -C "$APP_DIR" rev-parse --short HEAD)"

# 2. .env chỉ sinh LẦN ĐẦU, secret tạo ngay trên máy chủ và không rời khỏi đây.
if [[ ! -f "$ENV_FILE" ]]; then
  log "sinh $ENV_FILE"
  (
    umask 077
    sed -e "s|DISANSO_DOMAIN|$DOMAIN|" \
        -e "s|^DB_PASSWORD=.*|DB_PASSWORD=$(openssl rand -hex 24)|" \
        -e "s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$(openssl rand -hex 24)|" \
        -e "s|^ANALYTICS_TOKEN=.*|ANALYTICS_TOKEN=$(openssl rand -hex 32)|" \
        -e "s|^SEED_DEMO_PASSWORD=.*|SEED_DEMO_PASSWORD=${SEED_DEMO_PASSWORD:-$(openssl rand -base64 12 | tr -d '/+=')}|" \
        -e "s|^APP_FORWARD_PORT=.*|APP_FORWARD_PORT=$PORT|" \
        "$VPS/env.vps.example" > "$ENV_FILE"
  )
fi

# 3. Nginx + chứng chỉ. Bước 3a cài site HTTP để certbot xác minh qua webroot (cùng cách các
#    site khác trên máy này), 3b xin chứng chỉ, 3c cài site đầy đủ có HTTPS.
if [[ ! -d "/etc/letsencrypt/live/$DOMAIN" ]]; then
  log "cài site HTTP tạm để xin chứng chỉ cho $DOMAIN"
  mkdir -p /var/www/html
  cat > "$NGINX_CONF" <<NGX
server {
    listen 80;
    server_name $DOMAIN;
    location /.well-known/acme-challenge/ { root /var/www/html; }
    location / { return 503; }
}
NGX
  ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/disanso.conf
  nginx -t && systemctl reload nginx
  certbot certonly --webroot -w /var/www/html -d "$DOMAIN" --non-interactive --agree-tos --keep-until-expiring
fi

rendered="$(sed -e "s|DISANSO_DOMAIN|$DOMAIN|g" -e "s|DISANSO_PORT|$PORT|g" "$VPS/nginx-disanso.conf")"
if [[ ! -f "$NGINX_CONF" ]] || [[ "$(cat "$NGINX_CONF")" != "$rendered" ]]; then
  log "cài $NGINX_CONF"
  printf '%s\n' "$rendered" > "$NGINX_CONF"
  ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/disanso.conf
  nginx -t && systemctl reload nginx
fi

# 4. Đĩa: image mới ~1,5 GB. Cache build Docker trên máy này không có gì dùng (mọi image kéo
#    sẵn), dọn khi trống dưới 8 GB.
free_kb="$(df --output=avail / | tail -1)"
if (( free_kb < 8 * 1024 * 1024 )); then
  log "đĩa trống $((free_kb / 1024)) MB — dọn cache build Docker"
  docker builder prune -af > /dev/null
fi

# 5. Kéo image đúng tag vừa build và khởi động lại. --no-build: VPS không bao giờ build.
export DISANSO_IMAGE_WEB="ghcr.io/hiep-24105294/du-lich-web:${IMAGE_TAG}"
export DISANSO_IMAGE_ANALYTICS="ghcr.io/hiep-24105294/du-lich-analytics:${IMAGE_TAG}"
compose() { docker compose -f "$SRC/compose.yaml" -f "$VPS/compose.vps.yaml" --env-file "$ENV_FILE" "$@"; }
log "kéo image tag $IMAGE_TAG"
compose pull --quiet
compose up -d --no-build --remove-orphans
docker image prune -f > /dev/null

# 6. Chờ app lên. Lần đầu entrypoint migrate + seed + tải ảnh bìa từ Wikimedia, có thể vài phút.
for _ in $(seq 1 60); do
  if curl -fsS "http://127.0.0.1:${PORT}/healthz" > /dev/null 2>&1; then
    log "OK https://$DOMAIN  ($(curl -fsS "http://127.0.0.1:${PORT}/healthz"))"
    exit 0
  fi
  sleep 5
done
log "LỖI: app không trả /healthz sau 5 phút"
compose logs --tail 40 app
exit 1
