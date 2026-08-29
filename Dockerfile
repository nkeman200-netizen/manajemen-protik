# ============================================================
# Dockerfile — manajemen-protik (Laravel 11 / PHP 8.3-FPM)
# ============================================================

# Menggunakan image PHP 8.3 FPM
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www

# Instal dependensi sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev

# Bersihkan cache apt untuk meminimalkan ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP (PDO MySQL, Zip, GD, dll)
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instal Composer dari image resminya
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin seluruh kode aplikasi ke dalam container
COPY . /var/www

# Berikan permission ke folder storage dan bootstrap cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
