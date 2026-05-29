#!/bin/bash
set -e

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "vendor/ vide, lancement de composer install..."
    composer install --no-interaction --optimize-autoloader
fi

exec apache2-foreground