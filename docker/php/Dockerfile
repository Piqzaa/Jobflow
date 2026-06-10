FROM php:8.1-apache

# 1. Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Installation des extensions PHP MySQL et GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

# 3. Installation de l'extension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 4. Activation du mod_rewrite
RUN a2enmod rewrite

# 5. Configuration Apache
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 6. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 7. Copie des fichiers composer pour installation des dépendances
COPY composer.json composer.lock* ./

# 8. Installation des dépendances
RUN composer install --no-interaction --optimize-autoloader

# 9. Entrypoint
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
