# Dockerfile
FROM php:8.2-fpm-alpine

# Install dependencies sistem
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Install ekstensi PHP
# opcache sudah built-in di image php:8.2-fpm-alpine, tinggal enable
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip && \
    docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files dulu (untuk cache layer)
COPY composer.json composer.lock ./

# Install dependencies Laravel dulu sebelum copy semua file
# Supaya layer ini di-cache kalau composer.json tidak berubah
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy semua file project
COPY . .

# Copy package.json dulu untuk cache
COPY package*.json ./
RUN npm ci --prefer-offline

RUN npm run build

# Jalankan post-install scripts
RUN composer run-script post-autoload-dump || true

# Set permission
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Copy konfigurasi Nginx dan Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]