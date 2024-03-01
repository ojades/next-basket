#!/usr/bin/env bash

sed -i -e "s/html/users\/public/g" /etc/apache2/sites-enabled/000-default.conf
composer install --no-interaction
php artisan route:cache
php artisan config:cache
yes | php artisan migrate
touch /var/log/apache2/access.log
service apache2 start
tail -f /var/log/apache2/access.log
