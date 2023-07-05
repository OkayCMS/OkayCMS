FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        libxml2-dev \
        libssl-dev \
        libxslt-dev

RUN /usr/local/bin/docker-php-ext-install mysqli pdo pdo_mysql xmlwriter zip

# Install GD
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libxmp-dev
RUN docker-php-ext-configure gd \
                    --prefix=/usr \
                    --with-jpeg \
                    --with-webp \
                    --with-freetype;
RUN docker-php-ext-install gd

# Install XSL
RUN docker-php-ext-install xsl

# Install Xdebug
RUN pecl install xdebug-2.9.0 \
    && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
