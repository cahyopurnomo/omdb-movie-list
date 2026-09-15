FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/omdb-movie-list

# Copy custom PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy existing application directory contents
COPY . /var/www/html/omdb-movie-list

# Setup proper directory permissions
RUN mkdir -p /var/www/html/omdb-movie-list/storage/framework/sessions \
             /var/www/html/omdb-movie-list/storage/framework/views \
             /var/www/html/omdb-movie-list/storage/framework/cache \
             /var/www/html/omdb-movie-list/storage/logs \
             /var/www/html/omdb-movie-list/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/omdb-movie-list \
    && chmod -R 775 /var/www/html/omdb-movie-list/storage /var/www/html/omdb-movie-list/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
