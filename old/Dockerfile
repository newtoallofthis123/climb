# Dockerfile
FROM php:8.2-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json nginx.conf default.conf /var/www/

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    curl \
    libcurl4-openssl-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd curl intl xml zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www

# Run Composer Install
RUN composer install

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
