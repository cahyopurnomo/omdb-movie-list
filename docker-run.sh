#!/usr/bin/env bash

# Helper script untuk menjalankan Laravel 5 OMDb di Docker

set -e

COMMAND=$1

case "$COMMAND" in
  up|start)
    echo "🚀 Menjalankan container Docker (App PHP 7.4 & Web Nginx)..."
    docker compose up -d --build
    echo "✅ Container berjalan!"
    echo "👉 Akses aplikasi di: http://localhost:8080"
    ;;
  down|stop)
    echo "🛑 Menghentikan container Docker..."
    docker compose down
    ;;
  setup)
    echo "⚙️ Menjalankan setup awal Laravel di dalam container..."
    docker compose exec app composer install --optimize-autoloader
    docker compose exec app php artisan key:generate --force
    docker compose exec app php artisan migrate --seed --force
    docker compose exec app chmod -R 775 storage bootstrap/cache
    echo "✅ Setup selesai! Buka http://localhost:8080"
    ;;
  migrate)
    docker compose exec app php artisan migrate --force
    ;;
  seed)
    docker compose exec app php artisan db:seed --force
    ;;
  artisan)
    shift
    docker compose exec app php artisan "$@"
    ;;
  composer)
    shift
    docker compose exec app composer "$@"
    ;;
  bash|shell)
    docker compose exec app bash
    ;;
  logs)
    docker compose logs -f
    ;;
  *)
    echo "Penggunaan: ./docker-run.sh [perintah]"
    echo ""
    echo "Perintah yang tersedia:"
    echo "  up       : Membangun dan menjalankan semua container (background)"
    echo "  setup    : Menjalankan composer install, migrate & seed di container"
    echo "  down     : Menghentikan semua container"
    echo "  artisan  : Menjalankan artisan di container (contoh: ./docker-run.sh artisan route:list)"
    echo "  composer : Menjalankan composer di container (contoh: ./docker-run.sh composer install)"
    echo "  migrate  : Menjalankan database migration di container"
    echo "  seed     : Menjalankan database seeder di container"
    echo "  bash     : Masuk ke shell container PHP"
    echo "  logs     : Melihat realtime logs container"
    ;;
esac
