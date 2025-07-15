########################
# Étape 1 : builder Composer
########################
FROM composer:2 AS vendor

WORKDIR /app

# 1) Dépendances uniquement pour le cache
COPY composer.json composer.lock ./
# on désactive les scripts car bin/console n'est pas encore présent
RUN composer install --prefer-dist --no-progress --no-scripts

# 2) Copie **tout** le code de l’API
COPY . .

# 3) Installation finale (scripts OK, MakerBundle inclus)
RUN composer install --prefer-dist --no-progress

########################
# Étape 2 : image PHP-Apache
########################
FROM php:8.3-apache

# 1. Paquets nécessaires à la compilation des extensions
RUN apt-get update && apt-get install -y \
        libicu-dev         \
        zlib1g-dev         \
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

