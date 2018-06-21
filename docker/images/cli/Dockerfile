#######################################################################
# Docker php toolbox
# Contains php and composer : all the stuff to run reprise projects
#######################################################################

FROM php:7.1-cli

RUN apt-get update && \
    apt-get install -y libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng12-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug

RUN pecl install zip-1.14.0 \
    && docker-php-ext-enable zip

COPY php.ini /usr/local/etc/php/conf.d
