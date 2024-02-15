ARG BUILD_DEBUG=0
ARG BUILD_TEST=0
ARG PHP_INI=php.ini

# Install dependencies
FROM composer:1.10.19 as dependencies
COPY composer.* /app/
WORKDIR /app
RUN git config --global http.postBuffer 524288000
RUN composer install --no-interaction --ignore-platform-reqs --no-scripts --prefer-dist

FROM php:7.4.14-fpm
ARG BUILD_DEBUG
ARG BUILD_TEST
ARG PHP_INI

RUN apt-get update
RUN apt-get install -y libzip-dev libpq-dev zip unzip

# add zip to php
RUN docker-php-ext-install zip

# add postgresql
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN if [ "$BUILD_DEBUG" -eq "1" ]; then \
        # install xdebug and enable remote debugging
        pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug \
                && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
                && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
                && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    ; fi

RUN if [ "$BUILD_TEST" -eq "1" ] || [ "$BUILD_DEBUG" -eq "1" ]; then \
        # add git to allow cloning of logger
        apt-get install -y git && \
        # install composer
        curl -sS https://getcomposer.org/installer | php -- --version=1.10.19 --install-dir=/usr/local/bin --filename=composer \
    ; fi

WORKDIR /srv
RUN chown -R www-data /srv

COPY ./ ./
COPY ./docker/$PHP_INI /usr/local/etc/php/conf.d/custom-php.ini
COPY --from=dependencies /app/vendor ./vendor
