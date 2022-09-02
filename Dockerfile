FROM composer:latest AS composer
FROM php:8.1-fpm
COPY --from=composer /usr/bin/composer /usr/bin/composer
LABEL org.opencontainers.image.description="php-chmod is a PHP library for easily changing permissions recursively."
WORKDIR /app
COPY . .
RUN apt-get update && apt-get -y upgrade && apt-get -y install zip
RUN composer update
