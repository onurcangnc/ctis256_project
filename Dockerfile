# Use PHP 8.1 with Apache as the base image
FROM php:8.1-apache

# Update and install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mysqli zip \
    && docker-php-ext-enable mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Disable conflicting MPM modules
RUN a2dismod mpm_prefork mpm_worker mpm_event

# Enable the desired MPM module
RUN a2enmod mpm_prefork

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add ServerName directive to Apache configuration
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copy application files to the web directory
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
