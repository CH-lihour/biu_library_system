#!/usr/bin/env bash
# First-run bootstrap for the "app" container (CMD in docker/app/Dockerfile).
# Runs as the `sail` user.
# Every step is idempotent, so restarting the container is safe.

set -e

cd /var/www/html
export HOME=/home/sail

echo "[start] Preparing .env"
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "[start] Installing PHP dependencies"
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if ! grep -qE '^APP_KEY=.+' .env; then
    echo "[start] Generating APP_KEY"
    php artisan key:generate --force
fi

php artisan config:clear

echo "[start] Waiting for the database"
until php artisan tinker --execute='DB::connection()->getPdo();' >/dev/null 2>&1; do
    sleep 2
done

echo "[start] Running migrations"
php artisan migrate --force

if [ "$(php artisan tinker --execute='echo App\Models\User::count();' 2>/dev/null | tail -n 1)" = "0" ]; then
    echo "[start] Seeding database"
    php artisan db:seed --force
fi

if [ ! -e public/storage ]; then
    echo "[start] Linking storage"
    php artisan storage:link
fi

echo "[start] Building frontend assets"
if [ ! -d node_modules ]; then
    npm install --no-audit --no-fund
fi
npm run build

echo "[start] Starting Laravel on port 8000 (published as APP_PORT on the host)"
exec php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port=8000
