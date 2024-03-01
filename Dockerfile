#start with our base image (the foundation)
FROM php:8.2-apache

ARG user
ARG uid
ARG service=notifications

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    zip \
    unzip \
    vim

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

#set our application folder as an environment variable
ENV HOME /var/www/$service

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# enable apache module rewrite
RUN a2enmod rewrite

WORKDIR $HOME

COPY ./$service/. . 
RUN chown -R $user $HOME

COPY ./$service/bin/run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh