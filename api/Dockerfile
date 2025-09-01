########################
# Étape 1 : builder Composer
########################
FROM php:8.3-cli AS vendor

# Installer les dépendances nécessaires à l’extension mongodb
RUN apt-get update && apt-get install -y \
    git unzip pkg-config libssl-dev libcurl4-openssl-dev autoconf gcc make \
    && pecl install mongodb-1.19.3 \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-progress --no-scripts

COPY . .
RUN composer install --prefer-dist --no-progress


########################
# Étape 2 : image PHP-Apache
########################
FROM php:8.3-apache

# Installer l’extension mongodb et ses dépendances
RUN apt-get update && apt-get install -y \
        libicu-dev \
        zlib1g-dev \
        libssl-dev \
        git \
        unzip \
        pkg-config \
        libcurl4-openssl-dev \
        autoconf \
        gcc \
        make \
    && pecl install mongodb-1.19.3 \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. (facultatif) activer mod_rewrite
RUN a2enmod rewrite

# … et autorise .htaccess dans le docroot Symfony
RUN printf "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
</Directory>\n" \
    > /etc/apache2/conf-available/symfony-allow-htaccess.conf \
 && a2enconf symfony-allow-htaccess

RUN echo 'SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1' > /etc/apache2/conf-available/auth-header.conf \
    && a2enconf auth-header

WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN chown -R www-data:www-data /var/www/html/config/jwt
RUN chmod 644 /var/www/html/config/jwt/public.pem
RUN chmod 600 /var/www/html/config/jwt/private.pem

EXPOSE 80

