FROM richarvey/nginx-php-fpm

RUN mkdir -p /usr/src/php/ext/redis

RUN curl -L https://github.com/phpredis/phpredis/archive/3.0.0.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1

RUN echo 'redis' >> /usr/src/php-available-exts

RUN docker-php-ext-install redis

