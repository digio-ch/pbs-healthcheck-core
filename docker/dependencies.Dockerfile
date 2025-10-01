FROM composer:2.8.12

WORKDIR /srv
COPY composer.* .

RUN composer install --no-interaction --ignore-platform-reqs --no-scripts --prefer-dist
