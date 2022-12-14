
FROM composer:1.10.19 as dependencies

WORKDIR /srv
COPY composer.* .

RUN composer install --no-interaction --ignore-platform-reqs --no-scripts --prefer-dist
