FROM php:8.4-fpm-alpine

# Extensoes do Laravel + redis (cache/fila/sessao)
RUN apk add --no-cache git curl libpng-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Entrypoint que ajusta a posse de storage/bootstrap-cache para o www-data
# (o codigo vem por bind-mount, entao isso e feito em runtime, a cada start).
COPY entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
