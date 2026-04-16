# Status Code Coverage

Use this table to track which scenarios are planned, blocked, or verified.

Suggested meanings:

- `Planned` - test case identified but not run yet
- `Verified` - test was run and result captured
- `Blocked` - backend code is incomplete or broken
- `Mismatch` - actual result differs from contract

## Coverage table

| Endpoint | Scenario | Expected code | Current status | Verified date | Evidence | Notes |
|---|---|---:|---|---|---|---|
| `GET /api` | Basic health ping | 200 | Planned |  |  | Smoke endpoint |
| `GET /api/health` | Basic health ping | 200 | Planned |  |  | Smoke endpoint |
| `GET /` | Basic health ping (if root router exists) | 200 | Planned |  |  | Depends on entrypoint used |
| `GET /health` | Basic health ping (if root router exists) | 200 | Planned |  |  | Depends on entrypoint used |
| `POST /api/auth/register` | Valid new user | 200 or 201 | Planned |  |  | Current controller echoes user object but does not set 201 |
| `POST /api/auth/register` | Missing email | 400 | Planned |  |  | Validation case |
| `POST /api/auth/register` | Missing password | 400 | Planned |  |  | Validation case |
| `POST /api/auth/register` | Missing fullName | 400 | Planned |  |  | Validation case |
| `POST /api/auth/register` | Duplicate email | 409 | Planned |  |  | Based on repository lookup |
| `POST /api/auth/register` | Invalid JSON body | 400 | Planned |  |  | Backend should return `Invalid JSON body` |
| `POST /api/auth/register` | DB unavailable | 500 | Planned |  |  | Important negative test if local DB is not configured |
| `POST /api/auth/login` | Valid credentials | 200 | Planned |  |  | Returns user object on success |
| `POST /api/auth/login` | Wrong password | 401 | Planned |  |  | Invalid credentials |
| `POST /api/auth/login` | Unknown email | 401 | Planned |  |  | Invalid credentials |
| `POST /api/auth/login` | Missing email | 400 | Planned |  |  | Validation case |
| `POST /api/auth/login` | Missing password | 400 | Planned |  |  | Validation case |
| `POST /api/auth/login` | Invalid JSON body | 400 | Planned |  |  | Backend should return `Invalid JSON body` |
| `POST /api/auth/logout` | Logout after login | 200 | Planned |  |  | Should clear session and return `{ \"message\": \"Logged out\" }` |
| `POST /api/auth/logout` | Logout without session | 200 | Planned |  |  | Current implementation logs out regardless |
| Any unknown route | Nonexistent endpoint | 404 | Planned |  |  | Entry point fallback returns `NOT_FOUND` payload |
| `OPTIONS` preflight | Browser CORS preflight | 200 | Planned |  |  | Browser-driven behavior |

## Minimum coverage goal for Team E

At minimum, verify these first:

1. `GET /`
2. `GET /health`
3. `POST /api/auth/register` success
4. `POST /api/auth/register` duplicate email
5. `POST /api/auth/login` success
6. `POST /api/auth/login` invalid credentials
7. Unknown route `404`
8. CORS `OPTIONS`

## What to attach as evidence

For each verified row, keep one of these:

- Postman test result screenshot
- exported Postman run result
- raw response example
- short note with date, environment, and DB state
