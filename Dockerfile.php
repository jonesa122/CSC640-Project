FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Copy your PHP app files
COPY php/ /var/www/html/

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install mysqli

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Install JWT package
RUN composer require firebase/php-jwt:^6.0
