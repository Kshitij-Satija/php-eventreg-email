#!/bin/sh

# Ensure Composer is installed
if ! command -v composer &> /dev/null
then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Install PHP dependencies
composer install --ignore-platform-reqs

# Start the PHP built-in server
php -S 0.0.0.0:8080
