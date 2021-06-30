FROM php:8.0.3-fpm-alpine as sierra
LABEL maintainer="lukedavia@icloud.com"

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

RUN composer validate; \
    composer install --no-interaction --no-ansi --prefer-dist --no-progress; \
    composer dump-autoload --classmap-authoritative;

RUN set -ex && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS; \
    pecl install xdebug-3.0.4; \
    docker-php-ext-enable xdebug; \
    apk del .phpize-deps;

ENTRYPOINT ["docker-php-entrypoint", "php-fpm"]