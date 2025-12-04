+++
title = "Users Endpoint Overview"
description = "Register and login functionality"
weight = 5
+++
# 👤 Users Endpoint Overview

The frontend accesses:

### `http://localhost/index.php?endpoint=users`

This endpoint supports:

- `POST` → register a user, login

# 📝 How Registration Works

The frontend form sends a POST request to:

### `http://localhost/index.php?endpoint=users&action=register`

with a JSON body like:

```json
{
  "username": "newuser",
  "email": "newuser@example.com",
  "password": "securepass"
}
```

### ✅ What It Returns

If successful:
```json
{
  "success": true,
  "id": 4
}
```
If unsuccesful:
```json
{
  "error": "Missing required fields"
}
```
```json
{
  "error": "Account creation failed"
}
```

# 🔑 How Login Works

The frontend sends a POST request to:

### `http://localhost/index.php?endpoint=users&action=login`

with a JSON body like:

```json
{
  "email": "newuser@example.com",
  "password": "securepass"
}
```

### ✅ What It Returns

If successful:
```json
{
  "success": true,
  "token": "JWT_TOKEN_HERE"
}
```
If unsuccessful:
```json
{
  "error": "Invalid credentials"
}
```
```json
{
  "error": "Missing email or password"
}
```