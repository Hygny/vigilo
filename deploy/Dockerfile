FROM php:8.4-fpm-alpine

# Extensoes do Laravel + redis (cache/fila/sessao)
# libpq-dev: cliente do PostgreSQL (base CNPJ da V2, conexão `cnpj` via pdo_pgsql).
RUN apk add --no-cache git curl libpng-dev libzip-dev icu-dev oniguruma-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache

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
