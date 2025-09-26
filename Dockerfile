FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_sqlite opcache

    RUN a2enmod rewrite
    ENV APACHE_DOCUMENT_ROOT /var/www/html/public
    RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

    COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

    WORKDIR /var/www/html

    COPY ./src/ ./

    RUN composer install --no-dev --prefer-dist --optimize-autoloader \
      && php -r "file_exists('.env') || copy('.env.example', '.env');" \
      && mkdir -p storage/framework/{cache,session,views} storage/logs database \
      && touch database/database.sqlite \
      && chown -R www-data:www-data storage bootstrap/cache database

    COPY ./entrypoint.sh /entrypoint.sh
    RUN chmod +x /entrypoint.sh

    EXPOSE 80

    CMD ["/entrypoint.sh"]