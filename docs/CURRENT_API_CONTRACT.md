# E-Commerce Backend API Documentation

This document is generated from the current backend code in Ecomerece_Web_Backend.
It is the source of truth for React integration.

## Base URL

Use this base URL in development:

`http://localhost/IP2-Backend/Ecomerece_Web_Backend/public`

## Response Format

The API uses a shared response envelope from utils/responses.php.

Success:

```json
{
    "success": true,
    "data": {},
    "meta": {}
}
```

Error:

```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Readable message",
        "details": {}
    }
}
```

## Auth Model (Important for React)

This backend currently uses PHP sessions (cookie-based auth), not JWT.

For requests that depend on login state:

- Use `credentials: 'include'` in fetch.
- Send `Content-Type: application/json` for body requests.

Example fetch wrapper:

```js
const API_BASE = "http://localhost/IP2-Backend/Ecomerece_Web_Backend/public";

async function api(path, options = {}) {
    const res = await fetch(`${API_BASE}${path}`, {
        credentials: "include",
        headers: {
            "Content-Type": "application/json",
            ...(options.headers || {}),
        },
        ...options,
    });

    return res.json();
}
```

## Endpoint List

### Health

1. GET `/health.php`

What it does:

- Returns backend status and a DB check.

Request:

```js
api("/health.php", { method: "GET" });
```

### Authentication

1. POST `/api/auth/register`

What it does:

- Creates a new customer account.

Body:

```json
{
    "fullName": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "phone": "01000000000"
}
```

React request:

```js
api("/api/auth/register", {
    method: "POST",
    body: JSON.stringify({
        fullName: "John Doe",
        email: "john@example.com",
        password: "secret123",
        phone: "01000000000",
    }),
});
```

2. POST `/api/auth/login`

What it does:

- Authenticates user and creates PHP session.

Body:

```json
{
    "email": "john@example.com",
    "password": "secret123"
}
```

React request:

```js
api("/api/auth/login", {
    method: "POST",
    body: JSON.stringify({
        email: "john@example.com",
        password: "secret123",
    }),
});
```

3. POST `/api/auth/logout`

What it does:

- Clears session data and destroys session.

React request:

```js
api("/api/auth/logout", { method: "POST" });
```

4. GET `/api/auth/me`
5. GET `/api/me`

What they do:

- Both return the currently logged-in user from session.
- `/api/me` is an alias.

React request:

```js
api("/api/auth/me", { method: "GET" });
```

### Products

1. GET `/api/products`

What it does:

- Returns paginated/filtered products.

Query params:

- `q` optional search text
- `search` optional search text (also supported)
- `category` optional category
- `sortBy` optional field, default `name`
- `order` optional `asc` or `desc`, default `asc`
- `page` optional, default `1`
- `limit` optional, default `12`

React request:

```js
api("/api/products?page=1&limit=12&sortBy=name&order=asc", { method: "GET" });
```

2. GET `/api/products/{id}`

What it does:

- Returns one product by id.

React request:

```js
api("/api/products/1", { method: "GET" });
```

3. POST `/api/products` (Admin only)

What it does:

- Creates a product.
- Requires logged-in admin session.

Body example:

```json
{
    "name": "Running Shoe",
    "price": 120,
    "salePrice": 99,
    "stock": 15,
    "rating": 4.6,
    "images": ["/uploads/products/shoe1.png"]
}
```

React request:

```js
api("/api/products", {
    method: "POST",
    body: JSON.stringify({
        name: "Running Shoe",
        price: 120,
        salePrice: 99,
        stock: 15,
        rating: 4.6,
        images: ["/uploads/products/shoe1.png"],
    }),
});
```

4. PUT `/api/products/{id}` (Admin only)

What it does:

- Updates a product.
- Partial payload is accepted.

React request:

```js
api("/api/products/1", {
    method: "PUT",
    body: JSON.stringify({
        price: 110,
        stock: 20,
    }),
});
```

5. DELETE `/api/products/{id}` (Admin only)

What it does:

- Deletes a product.

React request:

```js
api("/api/products/1", { method: "DELETE" });
```

### Cart

All cart endpoints require logged-in session.

1. GET `/api/cart`

What it does:

- Returns current user cart (creates one if missing).

React request:

```js
api("/api/cart", { method: "GET" });
```

2. POST `/api/cart/items`

What it does:

- Adds item or updates item quantity.

Body:

```json
{
    "productId": 1,
    "quantity": 2
}
```

React request:

```js
api("/api/cart/items", {
    method: "POST",
    body: JSON.stringify({
        productId: 1,
        quantity: 2,
    }),
});
```

3. DELETE `/api/cart/items/{productId}`

What it does:

- Removes one product from cart.

React request:

```js
api("/api/cart/items/1", { method: "DELETE" });
```

### Orders

All order endpoints require logged-in session.

1. GET `/api/orders`

What it does:

- Lists orders for current user.

React request:

```js
api("/api/orders", { method: "GET" });
```

2. GET `/api/orders/{id}`

What it does:

- Returns one order if it belongs to current user.

React request:

```js
api("/api/orders/1", { method: "GET" });
```

3. POST `/api/orders`

What it does:

- Creates an order.
- Uses `items` from body when provided.
- If `items` is missing, it creates order from current cart items.

Body with explicit items:

```json
{
    "items": [
        { "productId": 1, "quantity": 2 },
        { "productId": 2, "quantity": 1 }
    ],
    "shipping": 10,
    "tax": 5
}
```

React request:

```js
api("/api/orders", {
    method: "POST",
    body: JSON.stringify({
        items: [
            { productId: 1, quantity: 2 },
            { productId: 2, quantity: 1 },
        ],
        shipping: 10,
        tax: 5,
    }),
});
```

## Common Frontend Failure Cases

- 401 UNAUTHORIZED:
  User is not logged in for cart/order/protected actions.

- 403 FORBIDDEN:
  Logged-in user is not admin for product create/update/delete.

- 404 NOT_FOUND:
  Entity id does not exist or endpoint path is wrong.

- 400 VALIDATION_ERROR:
  Invalid payload shape or required fields missing.

## Recommended React Integration Flow

1. App start: call GET `/api/auth/me`.
2. If unauthorized, show login.
3. On login success, reload user profile with `/api/auth/me`.
4. Use `credentials: 'include'` on every request.
5. Handle `success: false` responses centrally in one API client.
