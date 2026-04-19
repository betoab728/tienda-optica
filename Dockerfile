FROM php:8.3-cli

# -------------------------
# Dependencias base
# -------------------------
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    unzip \
    unixodbc-dev \
    libgssapi-krb5-2 \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install zip

# -------------------------
# Microsoft SQL Server driver
# -------------------------
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/11/prod bullseye main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql17

# -------------------------
# Extensiones PHP
# -------------------------
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# -------------------------
# Composer
# -------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# -------------------------
# Instalar dependencias Laravel
# -------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------
# 🔥 FRONTEND BUILD (VITE)
# -------------------------
RUN npm install && npm run build

# -------------------------
# Permisos
# -------------------------
RUN chmod -R 775 storage bootstrap/cache

# -------------------------
# Optimización Laravel
# -------------------------
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# -------------------------
# Puerto Railway
# -------------------------
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}