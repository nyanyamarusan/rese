set -e

cd /var/www/html

if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
    php artisan key:generate --force
fi

if grep -q '^DB_CONNECTION=' .env; then
  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
else
  echo "DB_CONNECTION=sqlite" >> .env
fi

if grep -q '^DB_DATABASE=' .env; then
  sed -i 's#^DB_DATABASE=.#DB_DATABASE=/var/www/html/database/database.sqlite#' .env
else
  echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env
fi

php artisan migrate --force || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php artisan storage:link || true

chown -R www-data:www-data storage bootstrap/cache database

exec apache2-foreground