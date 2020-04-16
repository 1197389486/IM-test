FROM php:7.2-fpm
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd
RUN pecl install swoole-4.4.5 && docker-php-ext-enable swoole
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pdo  pdo_mysql