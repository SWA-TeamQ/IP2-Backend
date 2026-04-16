# Status Code Coverage

Use this table to track which scenarios are planned, blocked, or verified.

Suggested meanings:

- `Planned` - test case identified but not run yet
- `Verified` - test was run and result captured
- `Blocked` - backend code is incomplete or broken
- `Mismatch` - actual result differs from contract

## Coverage table

| Endpoint | Scenario | Expected code | Current status | Notes |
|---|---|---:|---|---|
| `GET /` | Basic health ping | 200 | Planned | Should return `{ "status": "OK", "message": "API is running" }` |
| `GET /` | Wrong method like POST | 404 or method rejection | Planned | Router only maps GET |
| `GET /health` | Basic health ping | 200 | Planned | Should return `{ "status": "success", "message": "API working" }` |
| `GET /health` | Wrong method like POST | 404 or method rejection | Planned | Router only maps GET |
| `POST /api/auth/register` | Valid new user | 200 or 201 | Planned | Current controller echoes user object but does not set 201 |
| `POST /api/auth/register` | Missing email | 400 | Planned | Code checks required fields |
| `POST /api/auth/register` | Missing password | 400 | Planned | Code checks required fields |
| `POST /api/auth/register` | Missing fullName | 400 | Planned | Review copy checks fullName explicitly |
| `POST /api/auth/register` | Duplicate email | 409 | Planned | Based on repository lookup |
| `POST /api/auth/register` | Invalid JSON body | 400 or 500 | Planned | Should ideally be 400; actual code may not handle cleanly |
| `POST /api/auth/register` | DB unavailable | 500 | Planned | Important negative test if local DB is not configured |
| `POST /api/auth/login` | Valid credentials | 200 | Planned | Returns user object on success |
| `POST /api/auth/login` | Wrong password | 401 | Planned | Explicit in controller |
| `POST /api/auth/login` | Unknown email | 401 | Planned | Explicit in controller |
| `POST /api/auth/login` | Missing email | 400 or 500 | Planned | Code does not safely validate before DB query |
| `POST /api/auth/login` | Missing password | 400 or 401 | Planned | Depends on runtime behavior |
| `POST /api/auth/login` | Invalid JSON body | 400 or 500 | Planned | Should be hardened by backend |
| `POST /api/auth/logout` | Logout with active session | 200 | Blocked | Route exists, method appears missing |
| `POST /api/auth/logout` | Logout without session | 200 or 401 | Blocked | Contract not defined in code |
| Any unknown route | Nonexistent endpoint | 404 | Planned | Root router and legacy public entry both define 404 behavior |
| `OPTIONS` preflight | Browser CORS preflight | 200 | Planned | Middleware/public entry explicitly allows OPTIONS |

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
