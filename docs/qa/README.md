# QA & Docs Deliverables

This folder contains local-only QA and documentation assets for Team E.

## Files

- `ENDPOINT_INVENTORY.md` - implemented endpoints and what to test
- `STATUS_CODE_COVERAGE.md` - scenario-by-scenario response coverage
- `ERROR_CATALOG.md` - known and expected error cases
- `CONTRACT_VERIFICATION.md` - backend vs frontend contract comparison
- `postman/shoplight-auth-smoke.postman_collection.json` - starter Postman collection

## Current scope

Based on the current backend code, only a small set of endpoints is wired:

- `GET /`
- `GET /health`
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`

Important notes:

- `logout` is routed but there is no `logout()` method in the current `AuthController`, so it is not yet verifiable as a successful feature.
- The auth controller file currently appears malformed in the working copy, so some runtime behavior may fail until the backend team fixes syntax/runtime issues.
- The contract in `docs/API_CONTRACT.md` is broader than the current implementation. This folder tracks both implemented behavior and contract gaps.

## How to use this folder

1. Start by reading `ENDPOINT_INVENTORY.md`.
2. Import the Postman collection and set the `baseUrl` variable.
3. Run each request and record actual results in the status coverage and contract verification docs.
4. Update "Planned" items to "Verified" as teammates finish backend work.

## Local-only reminder

These files were prepared for your local workflow. Nothing here is committed or pushed unless you explicitly choose to do that later.
