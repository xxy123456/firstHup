FROM php:7.2-fpm
COPY . /home
RUN pecl install swoole-4.2.12 \
    && docker-php-ext-enable swoole