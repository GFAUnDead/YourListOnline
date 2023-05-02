FROM php:8.0-apache

# Install the necessary packages
RUN apt-get update && \
    apt-get install -y \
        libmcrypt-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libzip-dev \
        unzip \
        wget \
        git \
        nano \
        openssl \
        mariadb-server

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql intl zip exif pcntl bcmath gd

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Clone website files from GitHub
RUN git clone https://github.com/YourStreamingTools/YourListOnline.git /var/www/html/

# Create a new database and user
ENV MYSQL_ROOT_PASSWORD=ListROOTPassword123
ENV MYSQL_DATABASE=todolistdb
ENV MYSQL_USER=listuser
ENV MYSQL_PASSWORD=ListPassword123

# Automatically create the necessary database tables
COPY init.sql

# Expose ports 80 and 443
EXPOSE 80 443

# Start the Apache web server and MySQL database
CMD service mysql start && /usr/sbin/apache2ctl -D FOREGROUND