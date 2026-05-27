# ShopLight Frontend (React) — Overview

## Purpose
This document describes the planned **ShopLight React frontend** and how it will integrate with the **ShopLight PHP REST API** (this repository).

The current UI/UX was originally prototyped as a vanilla HTML/CSS/JS project (IP1-Frontend). The goal now is to deliver the same experience using a modern React architecture, while keeping the backend API clean, consistent, and secure.


---

## Frontend Overview

The planned React frontend will serve as the primary user interface for the application. It will interact with the PHP backend via a REST API.

## Key Features
- Responsive design
- State management with Redux
- Authentication and authorization via tokens

---

## Frontend Product Scope (Features)
The React frontend will implement the following end‑to‑end features:

### Catalog & Discovery
- Home page with “Featured Products” section
- Product listing page with:
  - Search (debounced)
  - Filter by category
  - Sort (price, rating, etc.)
- Product detail page with:
  - Image gallery (support multiple images)
  - Sale price support
  - Add to Cart
  - Favorites (wishlist)

### Cart & Checkout
- Cart drawer / cart page
- Update item quantities
- Remove items
- Checkout:
  - Order summary (subtotal, tax, shipping, total)
  - Place order (real backend integration)
  - Order confirmation / receipt page

### Authentication
- Register
- Login
- Logout
- Forgot password (request reset / reset)
- Persist auth session (JWT recommended)

### Admin (optional but supported by API design)
- Product CRUD:
  - Create product
  - Update product
  - Delete product
- Order list (admin)
- User list (admin)

---

## Frontend Routes (React Router)
Proposed routing (SPA):

- `/` — Home
- `/products` — Product listing/search/filter/sort
- `/products/:id` — Product detail
- `/cart` — Cart
- `/checkout` — Checkout
- `/auth/login` — Login
- `/auth/register` — Register
- `/auth/forgot-password` — Forgot password
- `/account/orders` — My orders (authenticated)
- `/admin/products` — Admin products (admin)
- `/admin/orders` — Admin orders (admin)

---

## State Management (Recommended)
- **Auth state**: access token + user profile (persisted in localStorage or httpOnly cookie depending on backend choice)
- **Cart state**:
  - Guest cart: stored locally
  - Authenticated cart: optionally synced to backend
- **Product data**: cached list + product details, pagination support

---

## Data Model Expectations (Frontend View)
The React UI expects these core domain models from the API:

### Product
- `id` (string or int)
- `name` (string)
- `description` (string)
- `images` (array of URLs)
- `details` (object), typically:
  - `category`
  - `brand` (optional)
  - `price`
  - `salePrice` (optional)
  - `rating` (optional)
  - `stock` (optional)

### Cart
- Items: `{ productId, quantity }`
- Pricing is calculated by the backend for authenticated checkout flows.

### User
- `id`
- `fullName`
- `email`
- `phone` (optional)
- `role` (`customer` | `admin`)

### Order
- `id`
- `userId`
- `items` (productId, name snapshot, unitPrice snapshot, quantity)
- `subtotal`, `tax`, `shipping`, `total`
- `status` (`pending`, `paid`, `shipped`, `delivered`, `cancelled`)
- timestamps

---

## API Integration Pattern (Frontend)
All requests are JSON over HTTP and follow REST patterns:

- Base URL (dev): `http://localhost:<port>/api`
- Authorization:
  - `Authorization: Bearer <access_token>` for protected endpoints

Error responses are standardized so the frontend can show meaningful messages.

See: `docs/API_CONTRACT.md`.