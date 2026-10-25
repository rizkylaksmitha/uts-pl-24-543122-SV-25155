# Stage 1: Ambil composer binary
FROM composer:latest AS composer

# Stage 2: Build FrankenPHP
FROM dunglas/frankenphp:php8.2-alpine

# Copy composer dari stage 1
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install ekstensi PHP yang dibutuhkan
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    opcache

WORKDIR /app

# Copy berkas composer
COPY composer.json composer.lock ./

# Install dependensi
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy seluruh source code
COPY . .

# Optimasi autoloader
RUN composer dump-autoload --optimize

# Set hak akses folder
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["frankenphp", "php-server", "-l", "0.0.0.0:8000", "-r", "public/"]