-- ShopLight SQLite seed data
-- Safe to run multiple times due to INSERT OR IGNORE + fixed IDs.

PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

INSERT OR IGNORE INTO users (id, full_name, email, phone, password, role, created_at) VALUES
(1, 'Admin User', 'admin@shoplight.local', '+251900000001', '$2y$10$8z2G4f8kD8kS8J1A9K4h9eR3F2uM8x9bWcXnYdL2pQmN7sT6rV1a2', 'admin', CURRENT_TIMESTAMP),
(2, 'Abel Mekonnen', 'abel@shoplight.local', '+251900000002', '$2y$10$8z2G4f8kD8kS8J1A9K4h9eR3F2uM8x9bWcXnYdL2pQmN7sT6rV1a2', 'customer', CURRENT_TIMESTAMP),
(3, 'Amira Abdurahman', 'amira@shoplight.local', '+251900000003', '$2y$10$8z2G4f8kD8kS8J1A9K4h9eR3F2uM8x9bWcXnYdL2pQmN7sT6rV1a2', 'customer', CURRENT_TIMESTAMP),
(4, 'Bemigbar Yehuwalawork', 'bemigbar@shoplight.local', '+251900000004', '$2y$10$8z2G4f8kD8kS8J1A9K4h9eR3F2uM8x9bWcXnYdL2pQmN7sT6rV1a2', 'customer', CURRENT_TIMESTAMP);

INSERT OR IGNORE INTO products (id, name, description, category, brand, price, sell_price, rating, stock, images, created_at) VALUES
(1, 'Premium Running Shoe', 'Lightweight running shoe for daily workouts.', 'Shoes', 'StrideX', 120.00, 99.00, 4.6, 40, '["/uploads/products/shoe1.jpg"]', CURRENT_TIMESTAMP),
(2, 'Classic White Sneakers', 'Minimal sneakers for everyday wear.', 'Shoes', 'UrbanStep', 85.00, 75.00, 4.3, 60, '["/uploads/products/shoe2.jpg"]', CURRENT_TIMESTAMP),
(3, 'Canvas Backpack', 'Durable backpack with laptop compartment.', 'Bags', 'CarryPro', 65.00, 59.00, 4.4, 35, '["/uploads/products/bag1.jpg"]', CURRENT_TIMESTAMP),
(4, 'Cotton Hoodie', 'Soft fleece-lined hoodie for cool evenings.', 'Clothing', 'NorthWear', 55.00, 49.00, 4.2, 50, '["/uploads/products/hoodie1.jpg"]', CURRENT_TIMESTAMP),
(5, 'Wireless Earbuds', 'Noise-reduction earbuds with charging case.', 'Electronics', 'SoundPeak', 150.00, 129.00, 4.5, 25, '["/uploads/products/earbuds1.jpg"]', CURRENT_TIMESTAMP),
(6, 'Sports Water Bottle', 'Insulated steel bottle, 750ml.', 'Accessories', 'HydroFit', 22.00, 18.00, 4.1, 100, '["/uploads/products/bottle1.jpg"]', CURRENT_TIMESTAMP);

INSERT OR IGNORE INTO carts (id, user_id, created_at) VALUES
(1, 2, CURRENT_TIMESTAMP),
(2, 3, CURRENT_TIMESTAMP);

INSERT OR IGNORE INTO cart_items (id, cart_id, product_id, quantity) VALUES
(1, 1, 1, 1),
(2, 1, 6, 2),
(3, 2, 4, 1),
(4, 2, 5, 1);

INSERT OR IGNORE INTO orders (id, user_id, status, subtotal, tax, shipping, total, created_at, updated_at) VALUES
(1, 2, 'pending', 117.00, 11.70, 5.00, 133.70, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 3, 'pending', 178.00, 17.80, 0.00, 195.80, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT OR IGNORE INTO order_items (id, order_id, product_id, quantity, price) VALUES
(1, 1, 2, 1, 75.00),
(2, 1, 6, 2, 18.00),
(3, 2, 4, 1, 49.00),
(4, 2, 5, 1, 129.00);

COMMIT;
