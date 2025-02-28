FROM php:8.1-apache

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем зависимости
COPY composer.json composer.lock ./

# Установка пакетов
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# Копируем исходный код
COPY . .

# Права доступа
RUN chown -R www-data:www-data tmp

RUN a2enmod rewrite && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf