# Architecture & Design Principles

The ShopLight Backend is built as a custom, lightweight PHP framework. It follows the **MVC (Model-View-Controller)** pattern with an additional **Service Layer** to keep logic decoupled.

## Directory Structure

- `app/Core/`: The engine of the application. Contains the Router, Request, Response, and Database connection logic.
- `app/controllers/`: Handles incoming HTTP requests, validates input, and returns responses.
- `app/services/`: **The Brains.** All complex business logic (e.g., payment processing, complex filtering) resides here.
- `app/models/`: Direct database interaction using PDO.
- `app/middleware/`: Security and request pre-processing (Permissions, CORS).
- `app/Helpers/`: Utility classes like `JWTHelper` and `Validator`.
- `public/`: The only directory accessible to the web. Contains `index.php` (Front Controller).
- `routes/`: Definitions for all API and Web routes.

## The Workflow of a Request

1. **Entry**: All requests hit `public/index.php`.
2. **Bootstrapping**: Env variables are loaded, and the `Router` is initialized.
3. **Routing**: The `Router` matches the URI against definitions in `routes/api.php`.
4. **Middleware**: Before the controller runs, the Router executes any assigned Middleware (e.g., `IsAuthenticated`).
5. **Controller**: The matched Controller method is called.
6. **Service**: The Controller delegates heavy lifting to a Service.
7. **Response**: The Controller returns a JSON response using the `Response` helper.

## Why this Structure?
- **Surgical Updates**: You can change the database engine in `Core/Database.php` without touching your business logic.
- **Testability**: Services can be tested independently of HTTP requests.
- **Security**: By using a `public/` folder, sensitive files like `.env` and source code are never exposed to the web.
