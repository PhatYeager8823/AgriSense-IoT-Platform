# 1. Dùng PHP 8.2 + Apache
FROM php:8.2-apache

# 2. Cài đặt thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 3. Cài PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Thiết lập thư mục làm việc
WORKDIR /var/www/html

# 6. Copy code vào container
COPY . .

# 7. Cài thư viện Laravel (bỏ qua dev để nhẹ)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 8. Cấp quyền ghi cho folder storage (QUAN TRỌNG ĐỂ UPLOAD ẢNH)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Cấu hình Apache để trỏ vào public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 10. Kích hoạt mod_rewrite
RUN a2enmod rewrite

# 11. Copy file cấu hình chạy khi khởi động (Thêm đoạn này)
# Tự động chạy lệnh Clear Cache và Storage Link mỗi khi Server bật lên
CMD bash -c "php artisan migrate --force && php artisan optimize:clear && php artisan storage:link && apache2-foreground"

# 11. Mở cổng 80
EXPOSE 80
