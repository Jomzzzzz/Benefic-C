FROM php:8.2-apache

# Install mysqli for MySQL support
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your code
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set recommended PHP.ini settings
RUN echo "display_errors=On\nerror_reporting=E_ALL" > /usr/local/etc/php/conf.d/error.ini

EXPOSE 80
