# E-Commerce API Documentation

This document outlines the exact API contract for the backend application (`Ecomerece_Web_Backend`) based on the latest architectural updates (JSON Web Tokens, utility standardizations, and SQLite routing).

## Base Format

### Headers

- **Content-Type:** `application/json`
- **Authorization:** `Bearer <YOUR_JWT_TOKEN>` (for protected routes)

### Standard Responses

All API responses follow a strict envelope structure.

#### Success Response (2xx HTTP Status Codes)

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "Product Name"
    },
    "meta": {
        "page": 1,
        "limit": 12,
        "total": 50
    }
}
```

_(Note: The `meta` object is optional and usually used for pagination)._

#### Error Response (4xx, 5xx HTTP Status Codes)

```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Human readable message describing what went wrong",
        "details": {
            "email": "Email is already registered"
        }
    }
}
```

---

## 1. Authentication Endpoints

### 1.1 Register

**Method**: `POST /api/auth/register`
**Auth Required**: No

**Request Body**:

```json
{
    "fullName": "John Doe",
    "email": "john@example.com",
    "password": "securepassword",
    "phone": "1234567890" // Optional
}
```

**Response (201 Created)**:

```json
{
    "status": "success",
    "data": {
        "token": "eyJ0...token...",
        "user": {
            "id": 1,
            "fullName": "John Doe",
            "email": "john@example.com",
            "role": "customer"
        }
    }
}
```

### 1.2 Login

**Method**: `POST /api/auth/login`
**Auth Required**: No

**Request Body**:

```json
{
    "email": "john@example.com",
    "password": "securepassword"
}
```

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "token": "eyJ0...token...",
        "user": {
            "id": 1,
            "fullName": "John Doe",
            "email": "john@example.com",
            "role": "customer"
        }
    }
}
```

### 1.3 Get Current User

**Method**: `GET /api/auth/me` (or `GET /api/me`)
**Auth Required**: Yes (Bearer Token)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "user": {
            "id": 1,
            "fullName": "John Doe",
            "email": "john@example.com",
            "role": "customer"
        }
    }
}
```

### 1.4 Logout

**Method**: `POST /api/auth/logout`
**Auth Required**: No (Frontend handles token deletion)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "message": "Logged out successfully"
    }
}
```

---

## 2. Product Endpoints

### 2.1 List Products

**Method**: `GET /api/products`
**Auth Required**: No

**Query Parameters (Optional)**:

- `q` or `search`: Search term
- `category`: Filter by category
- `sortBy`: Field to sort by (default: 'name')
- `order`: 'asc' or 'desc' (default: 'asc')
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 12)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "items": [
            {
                "id": 1,
                "name": "Product 1",
                "price": 99.99,
                "salePrice": null,
                "stock": 10,
                "category": "Electronics"
            }
        ]
    },
    "meta": {
        "page": 1,
        "limit": 12,
        "total": 100
    }
}
```

### 2.2 Get Single Product

**Method**: `GET /api/products/{id}`
**Auth Required**: No

**Response (200 OK)**: Returns the single product object inside `data`.

### 2.3 Create Product

**Method**: `POST /api/products`
**Auth Required**: Yes (Admin only)

**Request Body**:

```json
{
    "name": "New Product",
    "price": 100.0,
    "salePrice": null,
    "stock": 50,
    "rating": 0,
    "images": ["url1.jpg"]
}
```

### 2.4 Update Product

**Method**: `PUT /api/products/{id}`
**Auth Required**: Yes (Admin only)

**Request Body**: Same fields as Create Product, but all are optional (partial update).

### 2.5 Delete Product

**Method**: `DELETE /api/products/{id}`
**Auth Required**: Yes (Admin only)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "deleted": true
    }
}
```

---

## 3. Cart Endpoints

### 3.1 Get Cart

**Method**: `GET /api/cart`
**Auth Required**: Yes (Bearer Token)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "userId": 1,
        "items": [
            {
                "productId": 5,
                "quantity": 2
            }
        ]
    }
}
```

### 3.2 Add or Update Cart Item

**Method**: `POST /api/cart/items`
**Auth Required**: Yes (Bearer Token)

**Request Body**:

```json
{
    "productId": 5,
    "quantity": 2
}
```

_(Note: If the product already exists in the cart, this updates its quantity)._

### 3.3 Remove Item from Cart

**Method**: `DELETE /api/cart/items/{productId}`
**Auth Required**: Yes (Bearer Token)

**Response (200 OK)**: Returns the updated cart state after removing the item.

---

## 4. Order Endpoints

### 4.1 List Orders

**Method**: `GET /api/orders`
**Auth Required**: Yes (Bearer Token)

**Response (200 OK)**:

```json
{
    "status": "success",
    "data": {
        "items": [
            {
                "id": 101,
                "userId": 1,
                "status": "pending",
                "total": 120.0
            }
        ]
    },
    "meta": {
        "total": 1
    }
}
```

### 4.2 Get Single Order

**Method**: `GET /api/orders/{id}`
**Auth Required**: Yes (Bearer Token)
_(Note: Only returns the order if it belongs to the authenticated user)._

### 4.3 Create Order

**Method**: `POST /api/orders`
**Auth Required**: Yes (Bearer Token)

**Request Body**:

```json
{
    "shipping": 10.0,
    "tax": 5.0,
    "items": [
        {
            "productId": 1,
            "quantity": 2
        }
    ]
}
```

_(Note: If the `items` array is omitted or not provided, the API automatically grabs all items from the current user's Cart and clears the Cart after the order is successfully created)._
