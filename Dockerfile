# Use official PHP image with Apache
FROM php:8.2-apache

# Copy your PHP website files into the container
COPY . /var/www/html/

# Enable Apache rewrite module if needed
RUN a2enmod rewrite
