# API Reference

All endpoints are prefixed with `/api`.

**Base URL**: `http://your-domain.com/api`

## 1. Authentication
| Method | Endpoint | Permission | Description |
| :--- | :--- | :--- | :--- |
| POST | `/auth/register` | `AllowAny` | Create a new user account. |
| POST | `/auth/login` | `AllowAny` | Returns a JWT token. |
| GET | `/auth/me` | `IsAuthenticated` | Returns current user profile. |

## 2. Products
| Method | Endpoint | Permission | Description |
| :--- | :--- | :--- | :--- |
| GET | `/products` | `AllowAny` | List all products (supports filters). |
| POST | `/products` | `IsAdminUser` | Create a new product (Admin only). |
| GET | `/products/{id}` | `AllowAny` | Get product details and reviews. |
| PATCH | `/products/{id}` | `IsAdminUser` | Update product. |
| DELETE | `/products/{id}` | `IsAdminUser` | Delete product. |

## 3. Reviews
| Method | Endpoint | Permission | Description |
| :--- | :--- | :--- | :--- |
| POST | `/products/{id}/reviews` | `IsAuthenticated` | Add a review. *Requires verified purchase.* |

## 4. Orders
| Method | Endpoint | Permission | Description |
| :--- | :--- | :--- | :--- |
| GET | `/orders` | `IsAuthenticated` | List user's own orders. |
| POST | `/orders` | `IsAuthenticated` | Place a new order. |
| GET | `/orders/all` | `IsAdminUser` | List all orders in the system. |

## 5. Admin
| Method | Endpoint | Permission | Description |
| :--- | :--- | :--- | :--- |
| GET | `/admin/stats` | `IsAdminUser` | Get dashboard statistics. |

## Standard Response Format

**Success:**
```json
{
    "status": "success",
    "message": "Operation successful",
    "data": { ... }
}
```

**Error:**
```json
{
    "status": "error",
    "message": "Reason for failure",
    "errors": [ ... ] 
}
```
