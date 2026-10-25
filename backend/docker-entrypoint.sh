#!/bin/sh
set -e

echo "Waiting for database..."
until nc -z -v -w30 db 3306; do
  echo "Waiting for MySQL database connection..."
  sleep 2
done

# Jalankan migrasi dan seeder
php artisan migrate --force
php artisan db:seed --force

# Jalankan server FrankenPHP
exec "$@"