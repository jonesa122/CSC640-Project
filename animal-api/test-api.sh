#!/bin/bash

# Base URL
BASE_URL="http://localhost:8000/index.php"

# Register a new user
echo "🔐 Registering user..."
curl -s -X POST "$BASE_URL?endpoint=users&action=register" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@example.com",
    "password": "securepass"
  }'

echo -e "\n\n🔑 Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL?endpoint=users&action=login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newuser@example.com",
    "password": "securepass"
  }')

TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token')
echo "✅ Token acquired: $TOKEN"

echo -e "\n\n📋 Listing all animals..."
curl -s "$BASE_URL?endpoint=animals"

echo -e "\n\n🔍 Searching for available dogs..."
curl -s "$BASE_URL?endpoint=animals&action=search&species=Dog&status=Available"

echo -e "\n\n📄 Getting animal with ID 1..."
curl -s "$BASE_URL?endpoint=animals&id=1"

echo -e "\n\n➕ Creating a new animal..."
curl -s -X POST "$BASE_URL?endpoint=animals&action=create" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Luna",
    "species": "Dog",
    "breed": "Beagle",
    "age": 2,
    "gender": "Female",
    "status": "Available"
  }'

echo -e "\n\n✏️ Updating animal with ID 1..."
curl -s -X PATCH "$BASE_URL?endpoint=animals&action=update&id=1" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "Adopted"
  }'

echo -e "\n\n🗑️ Deleting animal with ID 4..."
curl -s -X DELETE "$BASE_URL?endpoint=animals&action=delete&id=4" \
  -H "Authorization: Bearer $TOKEN"

echo -e "\n\n📝 Submitting adoption request..."
curl -s -X POST "$BASE_URL?endpoint=adoptions" \
  -H "Content-Type: application/json" \
  -d '{
    "animal_id": 2,
    "adopter_name": "Liam Smith",
    "adopter_phone": "555-5678",
    "adopter_email": "liam.smith@example.com",
    "adopter_address": "456 Oak Ave, Fort Mitchell, KY"
  }'

echo -e "\n\n📄 Getting adoption request with ID 1..."
curl -s "$BASE_URL?endpoint=adoptions&id=1"

echo -e "\n\n✏️ Updating adoption request with ID 1..."
curl -s -X PATCH "$BASE_URL?endpoint=adoptions&action=update&id=1" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "approved",
    "adopter_phone": "555-9999"
  }'