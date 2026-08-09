FROM php:8.4-cli-alpine

# Instalar dependencias del sistema y extensiones de PHP necesarias (pdo_mysql, bcmath)
RUN apk add --no-cache \
    bash \
    curl \
    git \
    netcat-openbsd \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql bcmath zip

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar archivos del proyecto
COPY . /app

# Instalar dependencias PHP de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Hacer ejecutable el script de inicio
RUN chmod +x /app/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
