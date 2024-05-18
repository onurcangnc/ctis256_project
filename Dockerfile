# Base image olarak PHP ve Apache kullanalım
FROM php:8.0-apache

# Gerekli PHP uzantılarını ve Composer'ı yükleyelim
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-install zip

# Composer'ı global olarak yükleyelim
RUN curl -sS https://getcomposer.org/installer | php -- 
--install-dir=/usr/local/bin --filename=composer

# Uygulama dosyalarını Apache web dizinine kopyalayalım
COPY . /var/www/html/

# Çalışma dizinini belirleyelim
WORKDIR /var/www/html

# Composer bağımlılıklarını yükleyelim
RUN composer install

# Apache portunu açalım
EXPOSE 80

# Apache'yi başlatalım
CMD ["apache2-foreground"]

