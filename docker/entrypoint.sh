#!/bin/sh
set -e

# If .env does not exist, copy from .env.example
if [ ! -f /app/.env ]; then
    cp /app/.env.example /app/.env
fi

# Ensure application key is present
if ! grep -q "^APP_KEY=base64:" /app/.env && [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run database migrations & seeders if database is accessible
echo "Menjalankan migrasi database..."
php artisan migrate --force --isolated

# Jalankan seeder agar user admin dan data awal inventaris langsung tersedia
php artisan db:seed --force || echo "Seeder dilewati."

# Optimize configuration caching for production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
fi

echo "Memulai server FrankenPHP pada port 8000..."
exec frankenphp php-server --listen :8000 --root /app/public

