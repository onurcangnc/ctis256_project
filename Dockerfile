# Base image olarak PHP ve Apache kullanalım
FROM php:8.1-apache

# Gerekli PHP uzantılarını yükleyelim
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip

# PHP uzantılarını yapılandıralım ve yükleyelim
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-install zip

# Composer'ı global olarak yükleyelim
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin 
--filename=composer \
    && rm composer-setup.php

# Apache ServerName direktifini ekleyelim
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Uygulama dosyalarını Apache web dizinine kopyalayalım
COPY . /var/www/html/

# Çalışma dizinini belirleyelim
WORKDIR /var/www/html

# Composer bağımlılıklarını yükleyelim
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

# Apache portunu açalım
EXPOSE 80

# Apache'yi başlatalım
CMD ["apache2-foreground"]

