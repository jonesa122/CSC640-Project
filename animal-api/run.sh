#!/bin/bash

# Exit immediately if a command fails
set -e

echo "🚀 Starting Laravel deployment..."

# Step 0: Stop and remove existing containers and volumes (fresh DB)
echo "🛑 Cleaning up old Docker containers and volumes..."
docker compose down -v

# Step 1: Install PHP dependencies
echo "📦 Installing composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Step 2: Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate:fresh --seed --force

# Step 3: Start Laravel server
echo "🌐 Starting Laravel server..."
php artisan serve --port=8000

