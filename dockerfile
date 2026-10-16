# Menggunakan image PHP resmi yang sudah dilengkapi dengan Apache web server
FROM php:8.2-apache

# Menginstall ekstensi mysqli dan pdo_mysql agar PHP bisa tersambung ke MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Menyalin seluruh file proyek Anda ke dalam folder web server di dalam Docker
COPY . /var/www/html/

# Membuka port 80 untuk akses web
EXPOSE 80
