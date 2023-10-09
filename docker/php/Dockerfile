# Use the base image of PHP 8.1 with Apache
FROM php:8.1-apache

# Update packages and install minimal dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends git libicu-dev libzip-dev libpng-dev libxml2-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install necessary PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql opcache intl zip

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the working directory
WORKDIR /var/www/html/
