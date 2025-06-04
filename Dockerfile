#
# Composer
#
FROM composer:2.6 as vendor

WORKDIR /app

COPY . .
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --ignore-platform-reqs \
    --prefer-dist

RUN composer dump-autoload


#
# Node frontend
#
FROM node:20.0-slim  as frontend

WORKDIR /app

COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm ci && npm run build
#RUN npm run build-mail


#
# Application build
#
FROM php:8.4-apache

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the ServerName directive globally
RUN echo "ServerName laravel-dto.app" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y \
 libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    iputils-ping \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd exif

# Install cron
RUN apt-get update && apt-get install -y cron

# Create the translations directory, make it editable, and assign ownership to www-data
RUN mkdir -p /var/www/html/resources/translations \
    && chown -R www-data:www-data /var/www/html/resources/translations \
    && chmod -R 775 /var/www/html/resources/translations

# Configure cron
COPY ./cronjobs /var/spool/cron/crontabs/root
RUN chmod 0600 /var/spool/cron/crontabs/root && chown root:root /var/spool/cron/crontabs/root

# Copy Frontend build
COPY --from=frontend /app/node_modules ./node_modules
COPY --from=frontend /app/public ./public
# Copy Composer dependencies
COPY --from=vendor   /app/vendor  ./vendor

# Copy the Laravel application files to the container
COPY . /var/www/html

# Change ownership to the web server user (www-data) and adjust permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Enable Apache rewrite module (repeat if necessary)
RUN a2enmod rewrite

# Copy the startup script into the image
COPY startup.sh /usr/local/bin/startup.sh

# Copy PHP configuration
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
RUN chown -R www-data:www-data /usr/local/etc/php/conf.d/uploads.ini

# Give execution rights on the startup script
RUN chmod +x /usr/local/bin/startup.sh

# Set the Apache DocumentRoot to your public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expose port 80 (HTTP)
EXPOSE 80

# Start cron and Apache in the foreground
CMD service cron start && apache2-foreground
CMD ["/usr/local/bin/startup.sh"]
