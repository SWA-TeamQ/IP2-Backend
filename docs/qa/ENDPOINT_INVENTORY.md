# Endpoint Inventory

This document lists the endpoints currently discoverable in the backend codebase and the QA actions that belong to Team E.

## Sources reviewed

- `public/index.php`
- `routes/api.php`
- `Ecomerece_Web_Backend/public/index.php`
- `Ecomerece_Web_Backend/routes/auth.php`
- `Ecomerece_Web_Backend/controllers/AuthController.php`
- `docs/API_CONTRACT.md`

## Base URLs in this repo

There are two entry styles in the repository:

1. Root router flow:
   - entry: `public/index.php`
   - routes: `routes/api.php`
   - expected base path: `/api`

2. Legacy auth-only flow:
   - entry: `Ecomerece_Web_Backend/public/index.php`
   - routes: `Ecomerece_Web_Backend/routes/auth.php`
   - auth paths checked directly against `/api/auth/...`

Because the repo has two structures, QA should confirm with the backend team which public entrypoint is the intended one for local testing.

## Currently implemented or wired endpoints

| Method | Path | Source | Current state | QA priority |
|---|---|---|---|---|
| GET | `/` | `routes/api.php` | Implemented | High |
| GET | `/health` | `routes/api.php` | Implemented | High |
| POST | `/api/auth/register` | `Ecomerece_Web_Backend/routes/auth.php` | Routed to controller | High |
| POST | `/api/auth/login` | `Ecomerece_Web_Backend/routes/auth.php` | Routed to controller | High |
| POST | `/api/auth/logout` | `Ecomerece_Web_Backend/routes/auth.php` | Routed, but controller method missing | High |
| GET | `/api/auth/me` | `IP2-Backend-Review` only | Not in main local backend flow | Medium |

## Contract-defined endpoints not yet visible in current local routing

These appear in `docs/API_CONTRACT.md` but are not currently visible as active routes in the main local backend:

- `POST /api/auth/forgot-password`
- `POST /api/auth/reset-password`
- `GET /api/me`
- `GET /api/products`
- `GET /api/products/:id`
- `POST /api/products`
- `PUT /api/products/:id`
- `DELETE /api/products/:id`
- `GET /api/favorites`
- `POST /api/favorites`
- `DELETE /api/favorites/:productId`
- `GET /api/cart`
- `POST /api/cart/items`
- `DELETE /api/cart/items/:productId`
- `POST /api/orders`
- `GET /api/orders`
- `GET /api/orders/:id`
- `GET /api/admin/orders`
- `POST /api/newsletter/subscribe`
- `POST /api/contact`

These should be marked as:

- `planned` if only documented
- `ready for QA` once routes and controller actions exist
- `verified` once tested successfully

## Endpoint-by-endpoint QA notes

### GET `/`

Expected current response from code:

```json
{
  "status": "OK",
  "message": "API is running"
}
```

What to test:

- returns `200`
- returns JSON
- contains `status` and `message`
- rejects unsupported methods if the router is handling correctly

### GET `/health`

Expected current response from code:

```json
{
  "status": "success",
  "message": "API working"
}
```

What to test:

- returns `200`
- returns JSON
- contains `status` and `message`

### POST `/api/auth/register`

Observed controller behavior:

- requires `email` and `password`
- also uses `fullName` while creating the user
- hashes password before insert
- returns `409` if email already exists
- returns `400` when required fields are missing

Minimum test cases:

- happy path with `fullName`, `email`, `password`, optional `phone`
- missing `email`
- missing `password`
- missing `fullName`
- duplicate email
- invalid JSON body
- unsupported content type

### POST `/api/auth/login`

Observed controller behavior:

- reads `email` and `password`
- checks user record from DB
- verifies password
- creates session on success
- returns `401` on invalid credentials

Minimum test cases:

- valid login
- wrong password
- unknown email
- missing `email`
- missing `password`
- invalid JSON body

### POST `/api/auth/logout`

Observed route behavior:

- route exists
- `logout()` method is not present in the current controller file

QA interpretation:

- this endpoint should be marked as `blocked`
- if called as-is, it may trigger a PHP fatal error or server-side failure
- do not claim this endpoint is implemented until backend confirms the method exists and runs

## Team E deliverables mapped to this inventory

For each endpoint above, Team E should provide:

1. A Postman request
2. At least one success-path assertion where possible
3. Negative test cases for validation/auth/404 behavior
4. A status-code entry in `STATUS_CODE_COVERAGE.md`
5. A contract match/mismatch note in `CONTRACT_VERIFICATION.md`
