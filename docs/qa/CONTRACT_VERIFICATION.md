# Contract Verification

This document compares the shared API contract with what is currently visible in the backend code.

## Verification legend

- `Match` - backend behavior appears aligned with contract
- `Partial` - backend has related behavior but not full contract alignment
- `Mismatch` - backend behavior clearly differs from contract
- `Missing` - endpoint is in the contract but not currently visible in local routes
- `Blocked` - route exists but code is incomplete/broken

## Response envelope comparison

### Contract expectation

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
    "message": "Human readable message",
    "details": {}
  }
}
```

### Current backend reality

- Root endpoints return simple payloads with `status` and `message`
- Auth endpoints return `user` directly on success
- Auth error payloads currently use `error.message` only

Result:

- overall response envelope consistency: `Mismatch`

## Endpoint contract matrix

| Endpoint | Contract expectation | Local backend observation | Result | Team E action |
|---|---|---|---|---|
| `GET /` | Not explicitly defined in contract | Exists and returns API-running message | Partial | Keep as smoke/health check |
| `GET /health` | Not explicitly defined in contract | Exists and returns API-working message | Partial | Keep as smoke/health check |
| `POST /api/auth/register` | Returns `success`, `data.user`, and normalized shape | Appears to return `{ "user": { ... } }` only | Mismatch | Document actual payload and recommend normalization |
| `POST /api/auth/login` | Returns `success`, `data.accessToken`, `data.user` | Appears to return only `user`, with session-based login | Mismatch | Flag that frontend expecting JWT will not match current backend |
| `POST /api/auth/logout` | Contract mentions logout as optional invalidation | Route exists, method appears missing | Blocked | Cannot verify until backend implements method |
| `POST /api/auth/forgot-password` | Contract defines endpoint | No route found | Missing | Keep in planned scope |
| `POST /api/auth/reset-password` | Contract defines endpoint | No route found | Missing | Keep in planned scope |
| `GET /api/me` | Contract defines protected session endpoint | No matching local route in main backend | Missing | Ask backend team whether `/api/auth/me` or `/api/me` is target |
| `GET /api/products` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/products/:id` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `POST /api/products` | Contract defines admin endpoint | No route found | Missing | Wait for backend implementation |
| `PUT /api/products/:id` | Contract defines admin endpoint | No route found | Missing | Wait for backend implementation |
| `DELETE /api/products/:id` | Contract defines admin endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/favorites` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `POST /api/favorites` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `DELETE /api/favorites/:productId` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/cart` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `POST /api/cart/items` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `DELETE /api/cart/items/:productId` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `POST /api/orders` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/orders` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/orders/:id` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `GET /api/admin/orders` | Contract defines endpoint | No route found | Missing | Wait for backend implementation |
| `POST /api/newsletter/subscribe` | Contract defines endpoint | No route found | Missing | Optional scope |
| `POST /api/contact` | Contract defines endpoint | No route found | Missing | Optional scope |

## Biggest contract gaps right now

1. Auth appears session-based in code, but contract says JWT is recommended and login response shows `accessToken`.
2. Error responses are not yet normalized to the documented error envelope.
3. Most contract endpoints are still documentation-only and not yet wired in routes.
4. The logout route is declared but not currently backed by an observable controller method.

## Questions to take to teammates

Use these in your team meeting or chat:

1. Which entrypoint should QA use locally: `public/index.php` or `Ecomerece_Web_Backend/public/index.php`?
2. Is auth supposed to be session-based now, or should it be JWT to match the contract?
3. Should `/api/me` or `/api/auth/me` be the final endpoint?
4. Do you want all responses standardized to the helper shape in `utils/responses.php`?
5. Is `logout()` still being implemented, or should the route be removed until ready?

## How Team E should update this file

For each backend update:

- move `Missing` to `Partial` or `Match`
- replace "appears to" with exact tested behavior
- attach one real sample response per verified endpoint
