# Architecture & Design Principles

The ShopLight Backend is built as a custom, lightweight PHP framework. It follows the **MVC (Model-View-Controller)** pattern with an additional **Service Layer** to keep logic decoupled.

## Directory Structure

- `app/Core/`: The engine of the application. Contains the Router, Request, Response, and Database connection logic.
- `app/Entities/`: **Domain Models.** Pure PHP classes (POPOs) representing data structures (User, Product, Order, etc.) with no knowledge of the database.
- `app/Repositories/`: **Data Access Layer.** Classes dedicated to reading and writing Entities to the database using SQL. Centralizes all database logic.
- `app/controllers/`: Handles incoming HTTP requests and extracts data from the `Request` object.
- `app/services/`: **Business Logic Layer.** Orchestrates Repositories and Entities to perform complex operations.

## The Workflow of a Request (Refined)

1. **Entry**: Request hits `public/index.php`.
2. **Routing**: `Router` matches URI and executes Middleware.
3. **Controller**: Extracts input and calls a Service.
4. **Service**:
   - Creates an **Entity** object from the input.
   - Calls a **Repository** to persist the Entity or fetch data.
5. **Repository**: Executes SQL via PDO and returns Entities or raw arrays.
6. **Response**: Controller returns JSON using `$this->success()` or `$this->error()`.

## Why this Structure?
- **Decoupling**: Business logic (Services) is separated from data storage (Repositories).
- **Testability**: Entities and Services can be unit tested without a database connection (by mocking repositories).
- **Maintainability**: All SQL for a specific concept (e.g., Products) is in one file (`ProductRepository.php`).

