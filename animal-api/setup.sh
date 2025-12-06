#!/bin/bash
set -e

# Stop and remove existing containers and volumes (fresh DB)
echo "🛑 Cleaning up old Docker containers and volumes..."
docker compose down -v

echo "🐳 Building and starting Docker containers..."
docker-compose up --build -d

echo "⏳ Waiting for MySQL to be ready..."
# Give the DB a few seconds to initialize
sleep 15

echo "🗄️ Running Laravel migrations inside the app container..."
docker-compose exec app php artisan migrate:fresh --seed --force


echo "✅ Laravel app is running at http://localhost:8000"
