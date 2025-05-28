FROM php:8.3-fpm

# Install system dependencies and PHP extensions for Laravel with MySQL support
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install PHP extensions for development (Xdebug)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Set recommended PHP.ini settings
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"] 