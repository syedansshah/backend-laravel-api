# Use an official PHP runtime as a parent image
FROM php:8.2-fpm

# Set the working directory in the container
WORKDIR /var/www/html

# Copy composer.lock and composer.json into the container
COPY composer.lock composer.json /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of your application code
COPY . /var/www/html

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html/storage

# Expose the port that the app runs on
EXPOSE 9000
