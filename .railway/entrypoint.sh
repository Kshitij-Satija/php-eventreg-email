#!/bin/sh

# Exit immediately if any command fails
set -e

# Ensure Composer is installed
if ! command -v composer &> /dev/null
then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Install PHP dependencies (ignore platform errors for Railway compatibility)
composer install --no-interaction --no-dev --optimize-autoloader --ignore-platform-reqs

# Start the PHP built-in server on Railway's expected port (default 3000)
php -S 0.0.0.0:${PORT:-3000}
