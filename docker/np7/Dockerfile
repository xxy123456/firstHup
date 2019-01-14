FROM richarvey/nginx-php-fpm:latest
COPY . /var/www/html
COPY ./docker/np7/nginx-sites /etc/nginx/nginx-sites-enabled

RUN apt-get update \
	# 手动安装依赖
	# 安装需要的扩展
   && pecl install swoole-4.2.12 \
   # 启用扩展
   && docker-php-ext-enable swoole
