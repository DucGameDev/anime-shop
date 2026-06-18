#!/bin/sh
set -e

echo "[entrypoint] Starting anime-shop production container..."

# ---------------------------------------------------------------------------
# Ensure runtime directories exist (needed by nginx on Alpine)
# /run/nginx is required by the default Alpine nginx for its pid file,
# but our custom nginx.conf writes to /run/nginx.pid directly — still safe.
# ---------------------------------------------------------------------------
mkdir -p /run/nginx /var/lib/nginx/tmp/client_body /var/lib/nginx/tmp/proxy \
         /var/lib/nginx/tmp/fastcgi /var/lib/nginx/tmp/uwsgi \
         /var/lib/nginx/tmp/scgi

# ---------------------------------------------------------------------------
# Generate nginx config from template — Railway sets $PORT dynamically.
# envsubst '${PORT}' only substitutes PORT, leaving nginx vars ($uri etc) intact.
# Output to http.d/ which Alpine nginx's main config includes.
# ---------------------------------------------------------------------------
PORT="${PORT:-8080}"
echo "[entrypoint] Configuring nginx on port $PORT..."
envsubst '${PORT}' \
    < /etc/nginx/templates/default.conf.template \
    > /etc/nginx/http.d/default.conf

# Validate nginx config before continuing (catches template errors early)
nginx -t

# ---------------------------------------------------------------------------
# Wait for database to become available (up to 30 attempts x 2s = 60s).
# We use a plain TCP check via PHP to avoid depending on artisan boot order.
# ---------------------------------------------------------------------------
echo "[entrypoint] Waiting for database..."
MAX_TRIES=30
TRIES=0

until php -r "
    \$host = getenv('DB_HOST') ?: '127.0.0.1';
    \$port = (int)(getenv('DB_PORT') ?: 3306);
    \$sock = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
    if (\$sock) { fclose(\$sock); exit(0); }
    exit(1);
" 2>/dev/null || [ "$TRIES" -ge "$MAX_TRIES" ]; do
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
# ---------------------------------------------------------------------------
echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction

# ---------------------------------------------------------------------------
# Create storage symlink: public/storage -> storage/app/public
# ---------------------------------------------------------------------------
echo "[entrypoint] Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# ---------------------------------------------------------------------------
# Ensure writable directories for Livewire uploads and Excel imports
# ---------------------------------------------------------------------------
mkdir -p storage/app/livewire-tmp storage/app/imports
chmod -R 775 storage/app/livewire-tmp storage/app/imports

# ---------------------------------------------------------------------------
# Cache config / routes / views for maximum performance.
# Must run AFTER APP_KEY and DB are confirmed available.
# ---------------------------------------------------------------------------
echo "[entrypoint] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ---------------------------------------------------------------------------
# Cache Filament panel components (optional — safe to skip if absent)
# ---------------------------------------------------------------------------
echo "[entrypoint] Caching Filament components..."
php artisan filament:cache-components 2>/dev/null || true

echo "[entrypoint] Boot complete. Handing off to supervisord (nginx + php-fpm)..."

# Hand off to CMD (supervisord) as PID 1
exec "$@"
