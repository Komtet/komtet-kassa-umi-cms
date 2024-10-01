FROM php:7.4-apache

RUN apt-get update
RUN apt-get install -y \
    zlib1g-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libfreetype6-dev \
    libgmp-dev \
    libpng-dev \
    libxslt-dev

RUN docker-php-ext-install mysqli gd xsl
RUN docker-php-ext-configure gd
RUN docker-php-ext-enable xsl

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY php .
