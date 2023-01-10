# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
FROM php:8.1-fpm-alpine as stage

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN set -eux  \
    && apk add --no-cache \
      acl fcgi file gettext git nano curl \
    && apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS icu-dev libzip-dev zlib-dev \
	\
	&& docker-php-ext-configure zip \
	&& docker-php-ext-install -j$(nproc) intl zip \
	&& docker-php-ext-enable opcache \
	\
	&& runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)" \
    && apk add --no-cache --virtual .pgsql-deps  \
      $runDeps postgresql-dev so:libpq.so.5 \
	&& docker-php-ext-install -j$(nproc) pdo_pgsql

RUN apk add oniguruma-dev \
    && docker-php-ext-install mbstring \
    \
    && apk add --repository http://dl-3.alpinelinux.org/alpine/edge/community  \
       rabbitmq-c-dev \
    && pecl install xdebug-3.1.4 amqp-1.11.0 \
    && docker-php-ext-enable xdebug amqp \
    \
    && apk del .build-deps \
    && pecl clear-cache \
    && rm -rf /tmp/pear \
    && docker-php-source delete \
    && rm -rf /var/cache/apk/*

RUN ln -s $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

WORKDIR /app

COPY docker/dev/php/conf.d/ $PHP_INI_DIR/conf.d/
COPY docker/dev/php/php-fpm.d/ /usr/local/etc/php-fpm.d/

COPY composer.json composer.lock* ./

RUN composer install --no-plugins --no-scripts --prefer-dist \
	&& composer clear-cache

COPY ./ ./

RUN composer run-script --no-dev post-install-cmd

HEALTHCHECK --interval=30s --timeout=5s --start-period=1s \
    CMD REQUEST_METHOD=GET SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping cgi-fcgi -bind -connect 127.0.0.1:9000

RUN chmod 0555 ./docker/docker-entrypoint.sh
ENTRYPOINT ["docker/docker-entrypoint.sh"]

CMD ["php-fpm"]
