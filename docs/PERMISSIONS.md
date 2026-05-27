# Permissions & Middleware

We use a declarative permission system inspired by Django REST Framework. Permissions are implemented as Middleware classes located in `app/middleware/`.

## Available Permission Classes

| Class | Description |
| :--- | :--- |
| `IsAuthenticated` | Requires a valid JWT token. |
| `IsAdminUser` | Requires a valid JWT token and the user must have the `admin` role. |
| `IsAuthenticatedOrReadOnly` | Open for `GET/HEAD/OPTIONS`. Requires auth for `POST/PUT/PATCH/DELETE`. |
| `IsAdminOrReadOnly` | Open for `GET/HEAD/OPTIONS`. Requires admin for `POST/PUT/PATCH/DELETE`. |
| `AllowAny` | Public access for all methods. |

## How to Apply Permissions

Permissions are assigned in `routes/api.php` using the `$router->middleware()` method.

### Example: Protecting a route
```php
// Only admins can delete products
$router->middleware('/products/([a-z0-9-]+)', IsAdminUser::class);
$router->delete('/products/([a-z0-9-]+)', [ProductController::class, 'delete']);
```

### Example: Read-Only for Public, Write for Admin
```php
// Public can view products, but only admin can create/edit
$router->middleware('/products', IsAdminOrReadOnly::class);
$router->get('/products', [ProductController::class, 'index']);
$router->post('/products', [ProductController::class, 'store']);
```

## Internal Mechanism
The `Router` evaluates middleware before the Controller. If a middleware returns anything other than `true` (usually a `401` or `403` error response), the request cycle is terminated immediately, and the controller is never executed.
