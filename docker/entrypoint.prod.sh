#!/bin/sh
set -e

echo "[entrypoint] Starting anime-shop production container..."

# ---------------------------------------------------------------------------
# Wait for database to become available (up to 30 attempts x 2s = 60s)
# ---------------------------------------------------------------------------
echo "[entrypoint] Waiting for database..."
MAX_TRIES=30
TRIES=0

until php artisan db:show --no-interaction 2>/dev/null | grep -q "Database" || [ "$TRIES" -ge "$MAX_TRIES" ]; do
    TRIES=$((TRIES + 1))
    echo "[entrypoint] Database not ready, attempt $TRIES/$MAX_TRIES..."
    sleep 2
done

if [ "$TRIES" -ge "$MAX_TRIES" ]; then
    echo "[entrypoint] ERROR: Database connection timeout after ${MAX_TRIES} attempts"
    exit 1
fi

echo "[entrypoint] Database ready."

# ---------------------------------------------------------------------------
# Run pending migrations
# --force bypasses the production confirmation prompt
# ---------------------------------------------------------------------------
echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction

# ---------------------------------------------------------------------------
# Create storage symlink: public/storage -> storage/app/public
# || true: harmless if symlink already exists in some setups
# ---------------------------------------------------------------------------
echo "[entrypoint] Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# ---------------------------------------------------------------------------
# Cache config / routes / views for maximum performance
# Must run AFTER APP_KEY and DB are confirmed available
# ---------------------------------------------------------------------------
echo "[entrypoint] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ---------------------------------------------------------------------------
# Cache Filament panel components (optional — safe to skip if command absent)
# ---------------------------------------------------------------------------
echo "[entrypoint] Caching Filament components..."
php artisan filament:cache-components 2>/dev/null || true

# ---------------------------------------------------------------------------
# Generate nginx config from template — Railway sets $PORT dynamically
# envsubst '${PORT}' only substitutes PORT, leaving nginx vars ($uri etc) intact
# ---------------------------------------------------------------------------
PORT="${PORT:-8080}"
echo "[entrypoint] Configuring nginx on port $PORT..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

echo "[entrypoint] Boot complete. Starting supervisord (nginx + php-fpm)..."

# Hand off to supervisord as PID 1
exec "$@"
