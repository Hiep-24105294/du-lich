#!/bin/bash
# Entrypoint: wait for MySQL, run migrations, seed once, cache config, start Apache.
set -e
cd /var/www/html

# 1. APP_KEY: generate once and persist in the storage volume (never committed to Git)
KEY_FILE=storage/app/.appkey
if [ -z "$APP_KEY" ]; then
  if [ ! -f "$KEY_FILE" ]; then
    php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;' > "$KEY_FILE"
  fi
  export APP_KEY="$(cat $KEY_FILE)"
fi

# 2. Wait for the database (max ~2 minutes)
echo "[entrypoint] waiting for database ${DB_HOST}:${DB_PORT:-3306} ..."
for i in $(seq 1 60); do
  if php -r 'try{new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT")?:3306),getenv("DB_USERNAME"),getenv("DB_PASSWORD"));exit(0);}catch(Exception $e){exit(1);}'; then
    break
  fi
  sleep 2
done

# 2b. Test database for PHPUnit (phpunit.xml uses DB_DATABASE=disanso_test). Needs root; ignore failure.
php -r '
try {
  $pdo = new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT")?:3306), "root", getenv("DB_ROOT_PASSWORD") ?: "root_secret");
  $user = getenv("DB_USERNAME") ?: "disanso";
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `disanso_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  $pdo->exec("GRANT ALL PRIVILEGES ON `disanso_test`.* TO `".str_replace("`", "", $user)."`@`%`");
  echo "[entrypoint] test database disanso_test ready\n";
} catch (Exception $e) { echo "[entrypoint] skip test database: ".$e->getMessage()."\n"; }
' || true

# 3. Migrate and seed (seed only when the roles table is empty)
php artisan migrate --force
if [ "$(php artisan app:is-seeded)" != "yes" ]; then
  echo "[entrypoint] seeding sample heritage data ..."
  php artisan db:seed --force
fi
php artisan storage:link >/dev/null 2>&1 || true
php artisan optimize:clear >/dev/null
php artisan optimize >/dev/null

# 4. Scheduled backups (daily) run through cron-like loop in background
if [ "${BACKUP_ENABLED:-true}" = "true" ]; then
  ( while true; do php artisan app:backup-db >> storage/logs/backup.log 2>&1; sleep 86400; done ) &
fi

echo "[entrypoint] starting web server"
exec "$@"
