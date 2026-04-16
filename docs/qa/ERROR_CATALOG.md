# Error Catalog

This document maps likely backend failures to status codes, JSON shapes, and frontend handling notes.

## Why this matters

Team E is responsible for helping the frontend understand:

- what error statuses can happen
- what JSON structure comes back
- what message should be shown to users
- where the backend currently deviates from the shared contract

## Standard contract shape

The documented contract in `docs/API_CONTRACT.md` expects errors like:

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

## Current observed backend behavior

The auth controller currently appears to return simpler payloads such as:

```json
{
  "error": {
    "message": "Email already registered"
  }
}
```

That means the current backend may be missing:

- top-level `success: false`
- machine-readable `error.code`
- structured `error.details`

This is a contract mismatch that should be tracked until standardized.

## Concrete example payloads (copy/paste)

These examples are meant to be pasted into the frontend team chat/docs so they know what to parse today.

### 400 - Invalid JSON body (auth)

```json
{
  "error": {
    "message": "Invalid JSON body"
  }
}
```

### 400 - Missing required fields (register)

```json
{
  "error": {
    "message": "Full Name, Email, and Password are required"
  }
}
```

### 409 - Email already registered (register)

```json
{
  "error": {
    "message": "Email already registered"
  }
}
```

### 401 - Invalid credentials (login)

```json
{
  "error": {
    "message": "Invalid credentials"
  }
}
```

### 404 - Endpoint not found (public entrypoint fallback)

```json
{
  "error": {
    "code": "NOT_FOUND",
    "message": "The requested endpoint does not exist."
  }
}
```

## Error catalog table

| Situation | Endpoint | Expected status | Current/likely payload | Frontend handling note | Contract state |
|---|---|---:|---|---|---|
| Missing required register fields | `POST /api/auth/register` | 400 | `{ "error": { "message": "Email and password required" } }` or similar | Show validation error near form | Mismatch |
| Missing `fullName` on register | `POST /api/auth/register` | 400 | Review copy uses `Full Name, Email, and Password are required` | Show required-field message | Mismatch |
| Duplicate email | `POST /api/auth/register` | 409 | `{ "error": { "message": "Email already registered" } }` | Tell user to log in instead | Mismatch |
| Invalid credentials | `POST /api/auth/login` | 401 | `{ "error": { "message": "Invalid credentials" } }` or `Invalid email or password` | Show login failure message | Mismatch |
| Unknown route | Any | 404 | Entry point returns `{ "error": { "code": "NOT_FOUND", "message": "..." } }` | Show generic not found | Partial mismatch |
| Logout clears session | `POST /api/auth/logout` | 200 | `{ "message": "Logged out" }` | Clear local auth state on success | Mismatch |
| Malformed JSON | Auth endpoints | 400 or 500 | Unclear, depends on PHP warning/error path | Show generic request error | Unverified |
| DB connection failure | Auth endpoints | 500 | PDO/server failure response | Show service unavailable/retry later | Unverified |
| CORS/preflight issue | Browser calls | 4xx browser-level or failed request | Browser blocks request | Frontend sees network error | Unverified |

## Recommended normalized error codes

Even if the backend does not return these yet, this is a good target for discussion with teammates:

| Status | Suggested `error.code` | Meaning |
|---|---|---|
| 400 | `VALIDATION_ERROR` | Request body missing/invalid fields |
| 401 | `INVALID_CREDENTIALS` | Email/password incorrect |
| 401 | `UNAUTHENTICATED` | Protected route without session/token |
| 403 | `FORBIDDEN` | User lacks permission |
| 404 | `NOT_FOUND` | Route or resource not found |
| 409 | `EMAIL_ALREADY_REGISTERED` | Register attempted with existing email |
| 422 | `INVALID_PAYLOAD` | JSON parsed but content invalid |
| 500 | `INTERNAL_SERVER_ERROR` | Unexpected server failure |
| 503 | `SERVICE_UNAVAILABLE` | DB or dependency unavailable |

## Frontend fallback guidance

Until the backend standardizes errors, frontend should safely handle:

1. `error.message`
2. `message`
3. raw non-JSON failure as a generic error

Suggested fallback user messages:

- `400`: "Please check the form and try again."
- `401`: "Your email or password is incorrect."
- `404`: "The requested service was not found."
- `409`: "That email is already registered."
- `500`: "Something went wrong on the server. Please try again later."

## What Team E should update after test runs

After running Postman:

- replace "likely payload" with real examples
- attach exact status codes observed
- note which environment was used
- mark whether the result matches the contract
