#!/bin/sh
set -e

# База SQLite живёт на подключённом томе /data и переживает пересборку образа
mkdir -p /data
if [ ! -f /data/database.sqlite ]; then
    touch /data/database.sqlite
fi
chown -R www-data:www-data /data

php artisan migrate --force

exec apache2-foreground
