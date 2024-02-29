#start with our base image (the foundation)
FROM php:8.2-apache

ARG user
ARG uid

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
ENV USERS_HOME /var/www/users
ENV NOTIFICATIONS_HOME /var/www/notifications

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# enable apache module rewrite
RUN a2enmod rewrite

WORKDIR $USERS_HOME
COPY ./users/. .
RUN chown -R $user $USERS_HOME

## install all PHP dependencies
RUN composer install --no-interaction

WORKDIR $NOTIFICATIONS_HOME
COPY ./notifications/. .
RUN chown -R $user $NOTIFICATIONS_HOME
RUN composer install --no-interaction

WORKDIR /var/www

COPY ./users/bin/run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh