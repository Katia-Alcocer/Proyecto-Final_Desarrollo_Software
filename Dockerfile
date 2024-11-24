FROM php:7.4-apache

# Instalación de extensiones de MySQL
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo pdo_mysql
