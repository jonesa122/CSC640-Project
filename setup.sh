#!/bin/bash
cp .env.docker .env
echo "🔍 Active DB_HOST is: $(grep DB_HOST .env | cut -d '=' -f2)"
set -e

echo "🐳 Building and starting Docker containers..."
docker-compose up --build -d

echo "⏳ Waiting for MySQL to be ready..."
# Give the DB a few seconds to initialize
sleep 15

echo "🗄️ Running Laravel migrations inside the app container..."
docker compose logs db
docker-compose exec db bash -c 'until mysqladmin ping -h "localhost" --silent; do sleep 2; done'
docker-compose exec app php artisan migrate:fresh --seed --force


echo "✅ Laravel app is running at http://localhost"
