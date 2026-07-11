FROM php:8.4-apache

# intl/zip нужны Laravel-экосистеме; sqlite и mbstring уже входят в базовый образ
RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libzip-dev unzip \
    && docker-php-ext-install intl zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Корень сайта — public/, включаем mod_rewrite для маршрутов Laravel
RUN a2enmod rewrite \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && sed -i 's/\r$//' docker/entrypoint.sh \
    && chmod +x docker/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["docker/entrypoint.sh"]
