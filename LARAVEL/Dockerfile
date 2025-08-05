# Railway Backend - Laravel
FROM php:8.2-cli

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    nginx \
    supervisor \
    postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto Laravel
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Variables de entorno para Railway
ENV APP_ENV=production \
    APP_DEBUG=false \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync \
    SAFE_MIGRATION_MODE=true \
    FRESH_INSTALL=false

# Crear .env mínimo y generar APP_KEY
RUN echo "APP_KEY=" > .env && php artisan key:generate --force

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Script de inicio optimizado
RUN echo '#!/bin/bash' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo 'if [ ! -z "$PGHOST" ]; then' >> /start.sh && \
    echo '    until pg_isready -h $PGHOST -p $PGPORT -U $PGUSER; do sleep 2; done' >> /start.sh && \
    echo '    php artisan migrate:safe --force' >> /start.sh && \
    echo 'fi' >> /start.sh && \
    echo 'php artisan config:cache' >> /start.sh && \
    echo 'php artisan serve --host=0.0.0.0 --port=${PORT:-8000}' >> /start.sh && \
    chmod +x /start.sh

# Exponer puerto para Railway
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8000}/api/health || exit 1

# Comando de inicio
CMD ["/start.sh"]
