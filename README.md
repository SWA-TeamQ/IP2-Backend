# IP2-Backend
Backend of the e-commerce website

## Contributors

| ID        | Name                  | Username   |
|-----------|-----------------------|------------|
| ETS0038/16 | Abel Mekonnen        | bella-247 |
| ETS0170/16 | Amira Abdurahman     | ami798 |
| ETS0243/16 | Bemigbar Yehuwalawork | Bem132833 |
| ETS0265/16 | Betelhem Kassaye     |betelhem16 |
| ETS0038/16 | Barok Yeshiber         | Barok-y |
| ETS 0240/16| Bekam Yoseph         | bekam-bit |



🛠️ ShopLight Backend — PHP E-commerce API




📌 One-line Summary

A lightweight, secure PHP backend API powering the ShopLight e-commerce platform, handling authentication, products, cart, and order management.

🔗 Related Project

🎨 Frontend Repo: https://github.com/E-commerce-foundation/FrontEnd

💡 Why This Project

This backend is designed to:

Complement the ShopLight frontend

Provide a real-world API structure using core PHP

Serve as a learning resource for backend fundamentals

It focuses on:

Clean architecture

Secure authentication

Scalable API design

Database-driven workflows

⚙️ Tech Stack

🐘 PHP (Core / Vanilla PHP)

🛢️ MySQL (or MariaDB)

🔐 JWT / Session-based Authentication (choose one)

🌐 RESTful API

🏁 Quick Start
1. Clone the repository
git clone https://github.com/E-commerce-foundation/ShopLight-Backend.git
cd ShopLight-Backend
2. Setup environment

Create a .env file:

DB_HOST=localhost
DB_NAME=shoplight
DB_USER=root
DB_PASS=yourpassword
JWT_SECRET=your_secret_key
3. Run locally

Using XAMPP / Laragon / PHP built-in server:

php -S localhost:8000

API will be available at:

http://localhost:8000
📁 Project Structure
/config         # Database & environment configs  
/controllers    # Request handlers (business logic)  
/models         # Database interaction logic  
/routes         # API route definitions  
/middleware     # Auth & request validation  
/utils          # Helpers (JWT, response formatting)  
/public         # Entry point (index.php)  
🔑 Core Features
🔐 Authentication

User registration & login

Password hashing (bcrypt)

Token/session management

📦 Products

Get all products

Get single product

Create / update / delete (admin)

🛒 Cart

Add to cart

Update quantity

Remove items

Persist cart per user

💳 Orders

Checkout system

Order creation

Order history

🔌 API Endpoints (Sample)
Auth
POST   /api/auth/register  
POST   /api/auth/login  
GET    /api/auth/profile  
Products
GET    /api/products  
GET    /api/products/{id}  
POST   /api/products        (admin)  
PUT    /api/products/{id}   (admin)  
DELETE /api/products/{id}   (admin)  
Cart
GET    /api/cart  
POST   /api/cart/add  
PUT    /api/cart/update  
DELETE /api/cart/remove  
Orders
POST   /api/orders  
GET    /api/orders  
🔒 Security Practices

Password hashing using password_hash()

Input validation & sanitization

Prepared statements (PDO/MySQLi) to prevent SQL injection

Token-based authentication

CORS configuration for frontend integration

🧪 Testing (Planned)

Unit tests for core logic

API testing with Postman / Thunder Client

Integration tests for auth & checkout

🔄 Development Workflow

Create feature branches:

git checkout -b feat/auth-system

Write clean, modular code

Test endpoints before pushing

Open PR with clear description

🤝 Contribution

Contributions are welcome!

Suggested Tasks

Add validation middleware

Improve error handling

Add API documentation (Swagger/Postman)

Implement role-based access control

🔗 Integration with Frontend

This backend is built to integrate with the ShopLight frontend:

Handles all API requests

Returns JSON responses

Designed for easy connection with fetch() or axios

📝 Environment Variables
DB_HOST=
DB_NAME=
DB_USER=
DB_PASS=
JWT_SECRET=
📜 License

MIT — see LICENSE

👥 Team

Maintainers and contributors will be listed here.



