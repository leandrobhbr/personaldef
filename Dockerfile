FROM php:8.5-apache

# PHP 8.5 já traz opcache, mbstring e pdo_sqlite embutidos

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev \
    && docker-php-ext-install zip intl pdo_mysql bcmath \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Roda o Apache com o mesmo UID/GID do usuário do host para evitar problemas de permissão no volume
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
