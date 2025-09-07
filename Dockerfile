FROM php:8.1-apache-bookworm

RUN apt-get update -y && apt-get install -y
RUN docker-php-ext-install pdo pdo_mysql \
	&& docker-php-ext-enable pdo_mysql

RUN docker-php-ext-install mysqli \
	&& docker-php-ext-enable mysqli

RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd
	
RUN apt-get install unzip -y

RUN apt-get install -y libzip-dev && docker-php-ext-install zip

RUN chown -R www-data:www-data /var/www/html

RUN a2enmod rewrite

RUN apachectl restart

# php.ini
COPY php.ini /usr/local/etc/php/

EXPOSE 80