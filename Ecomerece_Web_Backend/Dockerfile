# Stage 1: Build Dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY Ecomerece_Web_Backend/composer.json Ecomerece_Web_Backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Stage 2: Final Runtime
FROM php:8.2-fpm-alpine
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copy application code
COPY Ecomerece_Web_Backend/ .
# Copy vendor from build stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Optimize autoloader
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/html/public/uploads
EXPOSE 9000
CMD ["php-fpm"]
