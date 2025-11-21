FROM php:8.2-fpm

WORKDIR /var/www

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    git \
    curl \
    vim \
    locales \
    libreoffice \
    default-jre \
    autoconf \
    ca-certificates \
    gnupg2 \
    libssl-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# ImageMagick / imagick
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

# PHP-расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl sockets gd soap

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl sockets gd soap bcmath

# Xdebug (актуальная для PHP 8.2)
RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug

# Node.js 20.19.5 и npm 10.8.2
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@10.8.2

# Composer 2.8.12
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer self-update 2.8.12

# Пользователь
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Копирование кода
COPY --chown=www:www . /var/www

USER www

EXPOSE 9000
