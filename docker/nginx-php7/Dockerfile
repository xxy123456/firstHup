FROM richarvey/nginx-php-fpm

RUN mkdir -p /usr/src/php/ext/redis \
& curl -L https://github.com/phpredis/phpredis/archive/3.0.0.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
& echo 'redis' >> /usr/src/php-available-exts \
& docker-php-ext-install redis


ENV SKIP_COMPOSER=1
COPY . /var/www
COPY ./docker/nginx-php7/sites /etc/nginx/sites-enabled