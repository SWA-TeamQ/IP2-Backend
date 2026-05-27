# Stage 1: Build Dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY ecommerce-backend/composer.json ecommerce-backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Stage 2: Final Runtime
FROM php:8.2-fpm-alpine
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copy application code
COPY ecommerce-backend/ .
# Copy vendor from build stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Optimize autoloader
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/html/public/uploads
EXPOSE 9000
CMD ["php-fpm"]
