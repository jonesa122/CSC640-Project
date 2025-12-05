+++
title = "Animals Endpoint Overview"
description = "List, search, create, update, and delete animals"
weight = 6
+++
# 🐾 Animals Endpoint Overview

The frontend accesses:

### `http://localhost/index.php?endpoint=animals`

This endpoint supports:

- `GET` → list, view, or search animals
- `POST` → create a new animal
- `PATCH` → update an animal
- `DELETE` → remove an animal

## 🔍 GET All animals
`GET /index.php?endpoint=animals`  
→ Returns an array of all animals

```json
[
  {
    "id":"1",
    "name":"Bella",
    "species":"Dog",
    "breed":"Labrador Retriever",
    "age":"3",
    "gender":"Female",
    "arrival_date":"2025-10-01",
    "status":"Adopted"
  },
  {
    "id":"2",
    "name":"Max",
    "species":"Cat",
    "breed":"Siamese","age":"2","gender":"Male",
    "arrival_date":"2025-09-15",
    "status":"Fostered"
    }
  ]
```

## 🔍 Get animal by ID
  `GET /index.php?endpoint=animals&id=3`  
  → Returns one animal or 404 if not found

  ```json
  {
    "id":"3",
    "name":"Max",
    "species":"Cat",
    "breed":"Siamese","age":"2","gender":"Male",
    "arrival_date":"2025-09-15",
    "status":"Fostered"
  }
  ```

  ## 🔍 Search animals by attributes
  - Include whichever parameters and values are to be searched for, no need to include parameters that are not being searched
  `GET /index.php?endpoint=animals&action=search&name=Bella&breed=Retriever&species=Dog&age=3&gender=Female&status=Available`  
  → Returns filtered results

  ```json
   [
    {
      "id":"4",
      "name":"Bella",
      "species":"Dpg",
      "breed":"Retriever",
      "age":"3",
      "gender":"Female",
      "arrival_date":"2025-09-15",
      "status":"Available"
    }
  ]
  ```

  # ➕ Add New Animal

To add an animal, the frontend sends a POST request to:

`http://localhost/index.php?endpoint=animals&action=create`  

Requires JWT in `Authorization` header

```json
{
  "name": "Luna",
  "species": "Dog",
  "breed": "Beagle",
  "age": 2,
  "gender": "Female",
  "status": "Available"
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
  "error": "Insert failed"
}
```
```json
{
  "error": "Unauthorized"
}
```

# ✏️ Update Animal

To update an animal, the frontend sends a PATCH request to:

### `http://localhost/index.php?endpoint=animals&action=update&id=3`

Requires JWT in `Authorization` header

The JSON body includes only the fields to update:

```json
{
  "name": "Luna",
  "status": "Adopted"
}
```

# ✅ What It Returns

If successful:

```json
{
  "success": true,
  "updated_id": 3
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
  "error": "Unauthorized"
}
```
```json
{
  "error": "Update failed",
  "details": "SQL error message"
}
```

# 🗑️ Delete Animal

To delete an animal, the frontend sends a DELETE request to:

### `http://localhost/index.php?endpoint=animals&action=delete&id=3`

Requires JWT in `Authorization` header

# ✅ What It Returns

If successful:

```json
{
  "success": true,
  "deleted_id": 3
}
```
If unsuccessful:

```json
{
  "error": "Unauthorized"
}
```
```json
{
  "error": "Unauthorized"
}
```