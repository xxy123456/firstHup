FROM hooklife/docker-php-nginx:latest

COPY . /var/www
COPY ./docker/nginx-php7/nginx.conf /etc/nginx/nginx.conf

RUN pecl install swoole-4.2.12 \
    && docker-php-ext-enable swoole