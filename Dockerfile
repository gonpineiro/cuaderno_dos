# Partimos de la imagen php en su versión 7.4
FROM php:7.4-fpm

# Copiamos los archivos package.json composer.json y composer-lock.json a /var/www/
COPY composer*.json /var/www/

# Nos movemos a /var/www/
WORKDIR /var/www/

# Instalamos las dependencias necesarias
RUN apt-get update && apt-get install -y \
    build-essential \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl

# Instalamos extensiones de PHP
RUN docker-php-ext-install pdo_mysql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instalamos Xdebug
RUN apt-get update && apt-get install -y autoconf gcc make \
    && pecl install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug \
    && apt-get remove -y autoconf gcc make \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

# Copiamos configuración de Xdebug
COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Instalamos composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalamos dependendencias de composer
# RUN composer install --no-ansi --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts

# Copiamos todos los archivos de la carpeta actual de nuestra
# computadora (los archivos de laravel) a /var/www/
COPY . /var/www/

# Exponer puertos
EXPOSE 9000 9005

# Corremos el comando php-fpm para ejecutar PHP
CMD ["php-fpm"]
