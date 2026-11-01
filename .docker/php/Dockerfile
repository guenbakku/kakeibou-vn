FROM php:8.2-apache

LABEL author="guenbakku"

# Install required extensions
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install -j$(nproc) mysqli \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean

# Install composer
RUN apt-get install -y git zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean

# Enable apache rewrite module
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# Install Node.js and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_26.x | bash - \
    && apt-get install -y nodejs \
    && npm install --global yarn \
    && apt-get clean
