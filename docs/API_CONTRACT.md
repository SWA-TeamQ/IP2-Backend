# ShopLight API Contract (PHP REST API) — For React Frontend

## Overview
This document defines the **endpoints required** by the ShopLight React frontend.

- All responses are JSON: `Content-Type: application/json`
- All request bodies are JSON unless specified otherwise
- Base path: `/api`
- Authentication: JWT (recommended) using `Authorization: Bearer <token>`

---

## Standard Response Shape
### Success
```json
{
  "success": true,
  "data": {},
  "meta": {}
}
```

### Error
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Human readable message",
    "details": {}
  }
}
```

---

## Authentication & User Endpoints

### POST `/api/auth/register`
Create an account.

**Request**
```json
{
  "fullName": "John Doe",
  "email": "john@example.com",
  "phone": "+251...",
  "password": "StrongPassword1"
}
```

**Response**
```json
{
  "success": true,
  "data": {
    "user": { "id": 1, "fullName": "John Doe", "email": "john@example.com", "role": "customer" }
  }
}
```

---

### POST `/api/auth/login`
Login and obtain JWT.

**Request**
```json
{ "email": "john@example.com", "password": "StrongPassword1" }
```

**Response**
```json
{
  "success": true,
  "data": {
    "accessToken": "<jwt>",
    "user": { "id": 1, "fullName": "John Doe", "email": "john@example.com", "role": "customer" }
  }
}
```

---

### POST `/api/auth/logout`
Invalidate token (optional if stateless JWT).

---

### POST `/api/auth/forgot-password`
Trigger password reset flow.

**Request**
```json
{ "email": "john@example.com" }
```

**Response**
```json
{ "success": true, "data": { "message": "If the email exists, a reset link was sent." } }
```

---

### POST `/api/auth/reset-password`
Reset password using token.

**Request**
```json
{ "token": "<reset_token>", "newPassword": "NewStrongPassword1" }
```

---

### GET `/api/me` (protected)
Fetch current user session.

---

## Products Endpoints

> Note: The original frontend prototype currently tries `GET http://localhost:3000/products` and expects a JSON array of products. This backend should provide a canonical replacement under `/api/products`.

### GET `/api/products`
List products with optional search/filter/sort/pagination.

**Query params**
- `q` (string) search
- `category` (string)
- `sortBy` (e.g., `price`, `rating`, `name`)
- `order` (`asc|desc`)
- `page`, `limit`

**Response**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": "p1",
        "name": "Premium Shoe",
        "description": "…",
        "images": ["https://..."],
        "details": { "category": "Shoes", "price": 120, "salePrice": 90 }
      }
    ]
  },
  "meta": { "page": 1, "limit": 12, "total": 100 }
}
```

---

### GET `/api/products/:id`
Fetch one product.

---

### POST `/api/products` (admin)
Create product.

---

### PUT `/api/products/:id` (admin)
Update product.

---

### DELETE `/api/products/:id` (admin)
Delete product.

---

## Favorites (Wishlist) Endpoints

The prototype uses localStorage to store favorites; the React app can optionally sync favorites to the server.

### GET `/api/favorites` (protected)
Return product IDs or full product objects.

### POST `/api/favorites`
Add favorite.
```json
{ "productId": "p1" }
```

### DELETE `/api/favorites/:productId`
Remove favorite.

---

## Cart Endpoints (Optional Sync)
Guest cart can remain local, but authenticated users may want server cart sync.

### GET `/api/cart` (protected)
Return cart items.

### POST `/api/cart/items` (protected)
Add/update an item.
```json
{ "productId": "p1", "quantity": 2 }
```

### DELETE `/api/cart/items/:productId` (protected)
Remove item.

---

## Orders / Checkout Endpoints

### POST `/api/orders` (protected)
Create order from the cart or from provided items.

**Request (cart-based)**
```json
{
  "shippingAddress": {
    "fullName": "John Doe",
    "phone": "+251...",
    "address1": "Street...",
    "city": "Addis Ababa",
    "country": "ET"
  },
  "paymentMethod": "cod"
}
```

**Response**
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 101,
      "status": "pending",
      "subtotal": 200,
      "tax": 30,
      "shipping": 0,
      "total": 230
    }
  }
}
```

---

### GET `/api/orders` (protected)
List current user orders.

### GET `/api/orders/:id` (protected)
Order detail (for confirmation/receipt).

### GET `/api/admin/orders` (admin)
List all orders.

---

## Contact / Newsletter (Optional)
The footer UI contains a newsletter subscribe form. If you want it real:

### POST `/api/newsletter/subscribe`
```json
{ "email": "you@example.com" }
```

### POST `/api/contact`
```json
{ "name": "…", "email": "…", "message": "…" }
```