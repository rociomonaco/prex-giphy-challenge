#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

env_value() {
    grep -E "^$1=" .env | tail -n 1 | cut -d '=' -f 2- | tr -d '"'
}

DB_HOST="${DB_HOST:-$(env_value DB_HOST)}"
DB_PORT="${DB_PORT:-$(env_value DB_PORT)}"
DB_USERNAME="${DB_USERNAME:-$(env_value DB_USERNAME)}"
DB_PASSWORD="${DB_PASSWORD:-$(env_value DB_PASSWORD)}"
export DB_HOST DB_PORT DB_USERNAME DB_PASSWORD

attempts=0
until php -r '
    try {
        new PDO(
            sprintf("mysql:host=%s;port=%s", getenv("DB_HOST") ?: "mysql", getenv("DB_PORT") ?: "3306"),
            getenv("DB_USERNAME") ?: "prex",
            getenv("DB_PASSWORD") ?: "secret",
        );
    } catch (Throwable $e) {
        exit(1);
    }
'; do
    attempts=$((attempts + 1))
    if [ "$attempts" -ge 30 ]; then
        echo "Database is not reachable after ${attempts} attempts." >&2
        exit 1
    fi
    echo "Waiting for database..."
    sleep 2
done

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if grep -qE '^APP_KEY=$' .env; then
    php artisan key:generate --force
fi

php artisan migrate --force

if [ ! -f storage/oauth-private.key ]; then
    php artisan passport:keys
fi

php artisan db:seed --force

exec "$@"
