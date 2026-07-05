# API Specification - SmartWater Admin

## Base URL
```
http://localhost:8000/api
```

## Authentication
Currently no authentication implemented. All endpoints are public.

## Response Format
All responses are JSON with the following structure:

**Success (200)**:
```json
{
  "id": 1,
  "code": "AQ-RO-50",
  "name": "AquaPure RO 50",
  ...
}
```

**Error (4xx/5xx)**:
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Products API

### GET /api/products
List all products with pagination

**Response**:
```json
[
  {
    "id": 1,
    "code": "AQ-RO-50",
    "name": "AquaPure RO 50",
    "category_id": 1,
    "category": "Máy lọc nước RO",
    "model": "APRO-50",
    "capacity": "50 L/h",
    "unit": "Chiếc",
    "price": 3200000,
    "status": "active",
    "image_path": "AQ-RO-50.jpg",
    "created_at": "2026-07-05T16:02:16.000000Z"
  }
]
```

### GET /api/products/{id}
Get specific product

**Parameters**:
- `id` (integer) - Product ID

**Response**:
```json
{
  "id": 1,
  "code": "AQ-RO-50",
  "name": "AquaPure RO 50",
  "category_id": 1,
  "category": "Máy lọc nước RO",
  "model": "APRO-50",
  "capacity": "50 L/h",
  "unit": "Chiếc",
  "price": 3200000,
  "status": "active",
  "image_path": "AQ-RO-50.jpg",
  "created_at": "2026-07-05T16:02:16.000000Z"
}
```

### POST /api/products
Create new product

**Request Body**:
```json
{
  "code": "AQ-RO-200",
  "name": "AquaPure RO 200",
  "category_id": 1,
  "model": "APRO-200",
  "capacity": "200 L/h",
  "unit": "Chiếc",
  "price": 8200000,
  "status": "active",
  "image_path": "AQ-RO-200.jpg"
}
```

**Validation Rules**:
```
code       : required, max:50, unique
name       : required, max:150
category_id: required, exists:categories
model      : required, max:100
capacity   : required, max:50
unit       : optional, max:50
price      : required, integer, min:0
status     : required, in:active,maintenance,inactive
image_path : optional, max:255
```

**Response**: 201 Created with product object

### PUT /api/products/{id}
Update product

**Parameters**:
- `id` (integer) - Product ID

**Request Body**: Same as POST (all fields required)

**Response**: 200 OK with updated product object

### DELETE /api/products/{id}
Delete product

**Parameters**:
- `id` (integer) - Product ID

**Response**: 204 No Content

---

## Categories API

### GET /api/categories
List all categories

**Response**:
```json
[
  {
    "id": 1,
    "name": "Máy lọc nước RO",
    "description": "Dòng máy lọc thẩm thấu ngược",
    "status": "active",
    "created_at": "2026-07-05T16:02:16.000000Z"
  }
]
```

### GET /api/categories/{id}
Get specific category

**Response**: Single category object

### POST /api/categories
Create new category

**Request Body**:
```json
{
  "name": "Máy lọc nước nano",
  "description": "Dòng máy lọc công nghệ nano",
  "status": "active"
}
```

**Validation Rules**:
```
name       : required, max:100, unique
description: optional, max:500
status     : required, in:active,inactive
```

**Response**: 201 Created with category object

### PUT /api/categories/{id}
Update category

**Request Body**: Same as POST

**Response**: 200 OK with updated category object

### DELETE /api/categories/{id}
Delete category

**Response**: 204 No Content

---

## Inventories API

### GET /api/inventories
List all inventories

**Response**:
```json
[
  {
    "id": 1,
    "product_id": 1,
    "product": null,
    "code": "AQ-RO-50",
    "model": "APRO-50",
    "quantity": 42,
    "reserved": 8,
    "available": 34,
    "unit_cost": 2240000,
    "stock_status": "ok",
    "last_updated": "2026-07-05T16:02:16.000000Z"
  }
]
```

**Notes**:
- `available` = `quantity - reserved` (computed)
- `stock_status` (computed): 'out' if qty=0, 'low' if qty≤10, else 'ok'

### GET /api/inventories/{id}
Get specific inventory

**Response**: Single inventory object

### PATCH /api/inventories/{id}/adjust
Adjust inventory quantities

**Request Body**:
```json
{
  "quantity": 50,
  "reserved_quantity": 10,
  "unit_cost": 2240000
}
```

**Validation Rules**:
```
quantity           : required, integer, min:0
reserved_quantity  : required, integer, min:0
  Must be ≤ quantity
unit_cost          : required, numeric, min:0
```

**Response**: 200 OK with updated inventory object

---

## Error Responses

### 404 Not Found
```json
{
  "message": "Not found"
}
```

### 422 Unprocessable Entity (Validation Error)
```json
{
  "message": "The given data was invalid",
  "errors": {
    "code": ["The code must be unique"],
    "category_id": ["The category id must exist"]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Internal server error"
}
```

---

## Testing with cURL

### Get all products
```bash
curl http://localhost:8000/api/products
```

### Get product by ID
```bash
curl http://localhost:8000/api/products/1
```

### Create product
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "code": "TEST-001",
    "name": "Test Product",
    "category_id": 1,
    "model": "TEST",
    "capacity": "100",
    "price": 5000000,
    "status": "active"
  }'
```

### Update product
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{
    "code": "AQ-RO-50",
    "name": "Updated Name",
    "category_id": 1,
    "model": "APRO-50",
    "capacity": "50 L/h",
    "price": 3200000,
    "status": "active"
  }'
```

### Delete product
```bash
curl -X DELETE http://localhost:8000/api/products/1
```

### Adjust inventory
```bash
curl -X PATCH http://localhost:8000/api/inventories/1/adjust \
  -H "Content-Type: application/json" \
  -d '{
    "quantity": 50,
    "reserved_quantity": 5,
    "unit_cost": 2240000
  }'
```

---

## Rate Limiting
Not implemented. All endpoints have unlimited requests.

## Pagination
Currently endpoints return all records. Pagination can be added in future versions using:
```
GET /api/products?page=1&per_page=10
```

## Filtering & Sorting
Can be added in future versions:
```
GET /api/products?category_id=1&status=active
GET /api/products?sort=-price&order=desc
```
