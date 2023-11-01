FROM php:8.2-fpm

ADD ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN adduser --system --group laravel

RUN mkdir -p /var/www/html

ADD ./ /var/www/html

RUN chmod -R 777 /var/www/html/storage
RUN chmod -R 777 /var/www/html/bootstrap/cache

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

RUN chown laravel:laravel /var/www/html

