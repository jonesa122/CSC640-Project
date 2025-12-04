+++
title = "Adoptions Endpoint Overview"
description = "Submit and manage adoption requests"
weight = 7
+++
# 🐾 Adoptions Endpoint Overview

The frontend accesses:

### `http://localhost/index.php?endpoint=animals`

This endpoint supports:

- `GET` → adoption request by id
- `POST` → create a new adoption request
- `PATCH` → update an adoption request

# 📄 Get Adoption Request by ID

To retrieve a specific adoption request, the frontend sends a GET request to:

### `http://localhost/index.php?endpoint=adoptions&id=5`

This returns the full adoption record for the given ID, if it exists.

# ✅ What It Returns

If successful:

```json
{
  "id": 5,
  "animal_id": 2,
  "adoption_date": "2025-11-03",
  "adopter_name": "Liam Smith",
  "adopter_phone": "555-5678",
  "adopter_email": "liam.smith@example.com",
  "adopter_address": "456 Oak Ave, Fort Mitchell, KY",
  "status": "approved"
}
```
If unsuccessful:
```json
{
  "error": "Adoption request not found"
}
```
# 🐶 Submit Adoption Request

To submit an adoption request, the frontend sends a POST request to:

### `http://localhost/index.php?endpoint=adoptions`

with a JSON body like:

```json
{
  "animal_id": 2,
  "adopter_name": "Liam Smith",
  "adopter_phone": "555-5678",
  "adopter_email": "liam.smith@example.com",
  "adopter_address": "456 Oak Ave, Fort Mitchell, KY"
}
```

# ✅ What It Returns

If successful:

```json
{
  "success": true,
  "id": 5
}
```
If unsuccessful:
```json
{
  "error": "Invalid animal_id: no such animal exists"
}
```
```json
{
  "error": "Missing required fields"
}
```
```json
{
  "error": "Adoption request failed",
  "details": "SQL error message"
}
```

# ✏️ Update Adoption Request

To update an adoption request, the frontend sends a PATCH request to:

### `http://localhost/index.php?endpoint=adoptions&action=update&id=5`

Requires JWT in `Authorization` header

The JSON body includes any fields to update:

```json
{
  "status": "approved",
  "adopter_phone": "555-9999"
}
```

# ✅ What It Returns

If successful:

```json
{
  "success": true,
  "updated_id": 5
}
```
If unsuccessful:

```json
{
  "error": "No valid fields provided for update"
}
```
```json
{
  "error": "Invalid or expired token"
}
```

```json
{
  "error": "Failed to update adoption request",
  "details": "SQL error message"
}
```