ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-fpm

# Debian 11 (образ PHP 7.4) знято з підтримки, пакети лежать лише в archive.debian.org
RUN if grep -q bullseye /etc/os-release; then sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list; fi && \
    apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        libxml2-dev \
        libssl-dev \
        libxslt-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev && \
    rm -rf /var/lib/apt/lists/*

RUN /usr/local/bin/docker-php-ext-install mysqli pdo_mysql zip exif

# Install GD
RUN docker-php-ext-configure gd \
                    --prefix=/usr \
                    --with-jpeg \
                    --with-webp \
                    --with-freetype;
RUN docker-php-ext-install gd

# Install XSL
RUN docker-php-ext-install xsl

# Install Xdebug (3.1 — остання з підтримкою 7.4); вмикається через config/php/conf.d/xdebug.ini
RUN if [ "$(php -r 'echo PHP_MAJOR_VERSION;')" = "7" ]; then XDEBUG=xdebug-3.1.6; else XDEBUG=xdebug-3.5.3; fi \
    && pecl install "https://pecl.php.net/get/$XDEBUG.tgz"

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
