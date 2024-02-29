#!/usr/bin/env bash

sed -i -e "s/html/notifications\/public/g" /etc/apache2/sites-enabled/000-default.conf
php artisan route:cache
php artisan config:cache
php artisan mq:consume &
touch /var/log/apache2/access.log
service apache2 start
tail -f /var/log/apache2/access.log
