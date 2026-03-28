FROM php:7.3-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (needed for CodeIgniter)
RUN a2enmod rewrite