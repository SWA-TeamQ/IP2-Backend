# Postman Run Guide (Team E)

This guide explains how to run the Team E Postman collection and capture evidence for grading.

## What you need

- Postman installed
- Backend running locally (ask the backend team which entrypoint is active)
- A working MySQL database (for register/login to succeed)

## Import the collection

1. Open Postman
2. Import:
   - `docs/qa/postman/shoplight-auth-smoke.postman_collection.json`

## Set collection variables

In Postman, open the collection → **Variables**:

- `baseUrl`
  - Example (typical): `http://localhost:8000`
  - Example (XAMPP/Apache): `http://localhost/IP2-Backend/Ecomerece_Web_Backend/public`
- `qaEmail`
  - Use a unique value per run to avoid conflicts.
  - Tip: set it to something like `qa+<date>@example.com`.

## Recommended run order

1. `GET Root` or `GET /api` (smoke)
2. `GET Health` or `GET /api/health` (smoke)
3. `POST Register - Success`
4. `POST Register - Duplicate Email`
5. `POST Login - Success`
6. `POST Login - Invalid Password`
7. `POST Logout`
8. `GET Unknown Route - 404`

## How to capture evidence

For each request (or for a full run), capture at least one:

- Screenshot of the request + **Tests** tab showing pass/fail
- Exported collection run summary
- Raw response JSON copied into `STATUS_CODE_COVERAGE.md` notes

Suggested naming for screenshots:

- `evidence_YYYY-MM-DD_register_success.png`
- `evidence_YYYY-MM-DD_login_invalid.png`

## Common failures and what they mean

- `500` on register/login:
  - DB not configured/running, or server-side PHP error.
- `404` on auth endpoints:
  - Wrong base URL (entrypoint path is incorrect).
- CORS/network error:
  - Not a backend JSON error; browser blocked the call (frontend scenario).

