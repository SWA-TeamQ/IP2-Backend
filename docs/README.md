# System Documentation Index

Welcome to the ShopLight Backend documentation. This guide is designed to help team members understand the architecture, workflows, and implementation details of the system.

## Documentation Modules

1. [**Architecture & Design Principles**](ARCHITECTURE.md)
   - Directory structure, Core components (Router, Request, Response), and the MVC pattern used.
2. [**Permissions & Middleware**](PERMISSIONS.md)
   - How the Django-like permission system works and how to protect routes.
3. [**API Reference**](API_REFERENCE.md)
   - Endpoint definitions, request/response formats, and base URL configuration.
4. [**File Uploads & Services**](FILE_UPLOADS.md)
   - How images are handled and stored via `FileService`.
5. [**Database Schema**](../Ecomerece_Web_Backend/database/schema.sql)
   - SQL definitions for Users, Products, Orders, and Reviews.

## Quick Start for Developers
The backend is a custom PHP framework built to be lightweight and modular.
- **Entry Point**: `Ecomerece_Web_Backend/public/index.php`
- **Routing**: Defined in `Ecomerece_Web_Backend/routes/api.php`. All routes are prefixed with `/api`.
- **Permissions**: Django-like middleware system for route protection.
- **Error Handling**: Centralized global error handler (Express-like) in `public/index.php`.
