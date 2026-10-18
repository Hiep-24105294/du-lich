#!/bin/bash
# Entrypoint: wait for MySQL, run migrations, seed once, cache config, start Apache.
set -e
cd /var/www/html

# Mọi lệnh artisan/ghi tệp chạy dưới www-data (cùng user với Apache) để storage/logs,
# storage/app/** không bị root chiếm quyền -> Apache vẫn ghi được laravel.log.
run_as_www() {
  if command -v setpriv >/dev/null 2>&1; then
    setpriv --reuid=www-data --regid=www-data --init-groups -- "$@"
  else
    su -s /bin/sh www-data -c "$(printf '%q ' "$@")"
  fi
}

# Volume mới gắn vào (hoặc tệp do bản cũ tạo bằng root) -> trả quyền cho www-data trước
mkdir -p storage/logs storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 1. APP_KEY: generate once and persist in the storage volume (never committed to Git)
KEY_FILE=storage/app/.appkey
if [ -z "$APP_KEY" ]; then
  if [ ! -f "$KEY_FILE" ]; then
    run_as_www sh -c "php -r 'echo \"base64:\".base64_encode(random_bytes(32)).PHP_EOL;' > $KEY_FILE"
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
run_as_www php artisan migrate --force
if [ "$(run_as_www php artisan app:is-seeded)" != "yes" ]; then
  echo "[entrypoint] seeding sample heritage data ..."
  run_as_www php artisan db:seed --force
fi
# public/storage symlink nằm ngoài storage/ nên tạo bằng root là đủ
php artisan storage:link >/dev/null 2>&1 || true
run_as_www php artisan optimize:clear >/dev/null
run_as_www php artisan optimize >/dev/null

# 4. Lịch nền (thay cron): sao lưu ngay khi khởi động rồi chạy schedule:run mỗi phút
#    (app:expire-holds mỗi phút, app:backup-db 02:00 hằng ngày, ghi storage/logs/backup.log)
if [ "${BACKUP_ENABLED:-true}" = "true" ]; then
  (
    run_as_www sh -c 'php artisan app:backup-db >> storage/logs/backup.log 2>&1' || true
    while true; do
      sleep 60
      # Giới hạn scheduler.log ~1 MB để không phình volume
      run_as_www sh -c 'f=storage/logs/scheduler.log; [ "$(stat -c%s "$f" 2>/dev/null || echo 0)" -gt 1048576 ] && : > "$f"; php artisan schedule:run >> "$f" 2>&1' || true
    done
  ) &
fi

# Lần cuối bảo đảm mọi tệp sinh ra ở trên thuộc www-data
chown -R www-data:www-data storage bootstrap/cache

echo "[entrypoint] starting web server"
exec "$@"
