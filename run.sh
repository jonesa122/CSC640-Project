#!/bin/bash

# Exit immediately if a command fails

cp .env.local .env
echo "🔍 Active DB_HOST is: $(grep DB_HOST .env | cut -d '=' -f2)"
set -e
echo "🚀 Starting Laravel deployment..."

# Step 1: Install PHP dependencies
echo "📦 Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Step 2: Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate:fresh --seed --force

# Step 3: Start Laravel server
echo "🌐 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=80


