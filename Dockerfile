FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs


FROM php:8.4-fpm-alpine AS app

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache unzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && docker-php-ext-install pdo_mysql bcmath opcache pcntl \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del .build-deps \
    && rm -rf /tmp/pear

COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY --from=vendor /usr/bin/composer /usr/bin/composer

RUN addgroup -g "${GID}" app \
    && adduser -D -u "${UID}" -G app app \
    && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

COPY --chown=app:app . .
COPY --from=vendor --chown=app:app /app/vendor ./vendor

USER app

RUN composer dump-autoload --optimize --no-scripts

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
