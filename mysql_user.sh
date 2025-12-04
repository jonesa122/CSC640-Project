#!/bin/bash

# Simple MySQL User Setup for Laravel

# Change root password if necessary
#ALTER USER 'root'@'localhost' IDENTIFIED BY 'YourNewPassword';
#FLUSH PRIVILEGES;

# Configuration - change these values
DB_NAME="laravel_app"
DB_USER="laravel_user"
DB_PASSWORD="password123"

echo "Creating MySQL user for Laravel..."

# Create database and user
if [[ "$OSTYPE" == "darwin"* ]]; then
  MYSQL="mysql"
else
  MYSQL="sudo mysql"
fi

$MYSQL -u root -p << EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "Done! Add this to your Laravel .env file:"
echo "DB_DATABASE=$DB_NAME"
echo "DB_USERNAME=$DB_USER"
echo "DB_PASSWORD=$DB_PASSWORD"