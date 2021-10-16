FROM php:8.0.11-apache

# System
RUN apt-get update
RUN apt-get install -y libzip-dev zip libpq-dev

# Files
COPY . /var/www/html/

# Apache
COPY kube/services/laravel/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP Extensions
RUN docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Install Dependencies
WORKDIR /var/www/html
RUN ls -la
RUN composer install --no-dev

# Permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html

EXPOSE 80