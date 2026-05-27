# ShopLight API Documentation (Comprehensive Integration Guide)

This document is the single source of truth for the ShopLight API. Use it to configure your API client (Axios, Fetch, etc.) and map your frontend state.

---

## 1. Global Technical Standards

### 1.1 Base URL & Routing
- **Base URL**: `http://your-domain.com/api`
- **Versioning**: All current routes are under the `/api` prefix.
- **Trailing Slashes**: The API is sensitive to trailing slashes; avoid adding them (e.g., use `/api/products` NOT `/api/products/`).

### 1.2 Data Formats
- **Request/Response Body**: Always JSON (UTF-8).
- **Date Format**: ISO 8601 (`YYYY-MM-DD HH:mm:ss`) in UTC.
- **Price Format (Crucial)**: 
  - Prices are integers representing **Cents**.
  - Example: $25.00 is sent/received as `2500`.
  - Frontend must divide by 100 to display correctly (e.g., `$25.00`).
- **IDs**: Identifiers are **UUIDs** (strings).
- **Image URLs**: The API automatically prepends the backend's base URL (configured via `APP_URL` in `.env`) to all image paths. Frontend should use them directly as fully qualified URLs.

---

## 2. Authentication Flow

The API uses **JWT (JSON Web Tokens)** for security.

### 2.1 Acquiring a Token
1. Call `POST /auth/login` or `POST /auth/register`.
2. On success, the API returns a `token` string in the `data` object.

### 2.2 Using the Token
Include the token in the `Authorization` header for all subsequent requests to protected routes.
```http
Authorization: Bearer <your_jwt_token>
```

---

## 3. Standard API Response Structure

Every response follows this wrapper structure to simplify frontend error handling.

### 3.1 Success (200, 201)
```json
{
    "status": "success",
    "message": "Human readable confirmation",
    "data": { ... } // Payload (Object, Array, or Null)
}
```

### 3.2 Error (400, 401, 403, 404, 500)
```json
{
    "status": "error",
    "message": "General error summary",
    "errors": [
        "Field 'email' is required",
        "Invalid password format"
    ] // Specific validation issues
}
```

---

## 4. Endpoint Deep Dive

### 4.1 Authentication Service

#### `POST /auth/register`
- **Description**: Create a new customer account.
- **Payload**:
  ```json
  {
    "firstName": "String (Required)",
    "lastName": "String (Required)",
    "email": "Valid Email (Required, Unique)",
    "password": "String (Required, Min 6 chars recommended)"
  }
  ```

#### `POST /auth/login`
- **Description**: Authenticate and receive a token.
- **Payload**:
  ```json
  {
    "email": "Valid Email (Required)",
    "password": "String (Required)"
  }
  ```
- **Return Data**: `{ "token": "...", "user": { ...UserEntity } }`

#### `GET /auth/me`
- **Auth**: Required (JWT)
- **Description**: Get the profile of the user currently logged in.

---

### 4.2 Product Service

#### `GET /products`
- **Auth**: None (Public)
- **Query Parameters**:
  - `category`: String (Filter by category name).
  - `search`: String (Fuzzy search in name/description).
  - `sortBy`: `price_cents`, `name`, or `created_at` (Default: `created_at`).
  - `sortOrder`: `ASC` or `DESC` (Default: `DESC`).
  - `limit`: Integer (Items per page, Default: 20).
  - `page`: Integer (Current page, Default: 1).

#### `GET /products/{id_or_slug}`
- **Auth**: None (Public)
- **Description**: Fetch detailed product info. The `{id_or_slug}` can be a UUID or the URL slug.
- **Includes**: The `reviews` array is automatically nested in the `data` object.

#### `POST /products` (Admin Only)
- **Auth**: Required (Admin Role)
- **Type**: `multipart/form-data`
- **Payload Fields**:
  - `name`: String (Required)
  - `description`: String (Required)
  - `price_cents`: Integer (Required)
  - `category`: String (Required)
  - `images[]`: File (Required, Multiple allowed)
  - `stock_quantity`: Integer (Default: 0)
  - `attributes`: JSON String (Optional, e.g. `{"color": "blue"}`)

---

### 4.3 Cart Service

#### `GET /cart`
- **Auth**: Required (JWT)
- **Description**: Retrieve current user's shopping cart.
- **Data Entity**: Array of objects containing `product_id`, `quantity`, and joined `name`, `price_cents`, `images`.

#### `POST /cart`
- **Auth**: Required (JWT)
- **Payload**: `{ "product_id": "UUID", "quantity": 1 }`
- **Logic**: If the product exists in the cart, the quantity is incremented.

---

### 4.4 Order & Checkout Service

#### `POST /orders`
- **Auth**: Required (JWT)
- **Description**: Finalize a purchase.
- **Payload**:
  ```json
  {
    "items": [
      { "productId": "UUID", "quantity": 2, "price": 1500 }
    ],
    "shippingAddress": {
      "fullName": "Jane Doe",
      "addressLine1": "123 Maple St",
      "city": "Springfield",
      "postalCode": "62704",
      "country": "USA"
    },
    "subtotal": 30.00, // Sent as Float/Decimal, Backend converts to 3000
    "tax": 2.50,
    "shipping": 5.00,
    "total": 37.50
  }
  ```

#### `GET /orders`
- **Auth**: Required (JWT)
- **Description**: Returns all orders placed by the authenticated user.

---

### 4.5 Admin Statistics Service

#### `GET /admin/stats`
- **Auth**: Required (Admin Role)
- **Return Data Structure**:
  ```json
  {
    "summary": { "total_orders": 150, "total_revenue": 500000, "total_customers": 45 },
    "recentSales": [...],
    "topProducts": [...],
    "stockAlerts": [...]
  }
  ```

---

## 5. Error Code Reference

| Status | Meaning | Typical Trigger |
| :--- | :--- | :--- |
| **400** | Bad Request | Missing required fields or invalid data format. |
| **401** | Unauthorized | Token missing, invalid, or expired. |
| **403** | Forbidden | User is authenticated but lacks permission (e.g. not an admin). |
| **404** | Not Found | Resource (Product/Order) does not exist. |
| **422** | Unprocessable Entity | Validation failed (check the `errors` array). |
| **500** | Server Error | Database crash or logic bug. Error details are returned in `message`. |

---

## 6. Frontend Integration Best Practices

1.  **Price Display**: Always use a helper to format prices.
    ```javascript
    const formatPrice = (cents) => `$${(cents / 100).toFixed(2)}`;
    ```
2.  **Image Paths**: The API returns relative paths like `/uploads/img_123.jpg`. You must prefix this with your server's host:
    ```javascript
    const imageUrl = `${API_HOST}${product.images[0]}`;
    ```
3.  **Order Summary**: The backend recalculates totals to prevent tampering. Ensure your frontend calculations (subtotal + tax + shipping) match the backend logic precisely to avoid `400` errors.
4.  **Slug Navigation**: For SEO-friendly URLs, prefer using the `slug` for navigation and the `id` for API actions like `POST /cart`.
