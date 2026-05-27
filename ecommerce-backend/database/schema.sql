-- ShopLight Database Schema (PostgreSQL)

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- 1. Users table
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    avatar_url TEXT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 2. Products table
CREATE TABLE IF NOT EXISTS products (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price_cents INTEGER NOT NULL,
    sale_price_cents INTEGER NULL,
    images TEXT[] NOT NULL,
    category VARCHAR(50) NOT NULL,
    rating NUMERIC(3,2) DEFAULT 0,
    review_count INTEGER DEFAULT 0,
    stock_quantity INTEGER DEFAULT 0,
    badge VARCHAR(50) NULL,
    attributes JSONB NOT NULL,
    features TEXT[] NOT NULL,
    highlights TEXT[] NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 3. Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id UUID NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 3.5 Cart table
CREATE TABLE IF NOT EXISTS cart (
    id SERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id UUID NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 4. Orders table
CREATE TABLE IF NOT EXISTS orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    subtotal_cents INTEGER NOT NULL,
    tax_cents INTEGER NOT NULL,
    shipping_cents INTEGER NOT NULL,
    total_cents INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'pending', -- pending, completed, shipped, cancelled
    shipping_address JSONB NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 5. Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id BIGSERIAL PRIMARY KEY,
    order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id UUID NOT NULL REFERENCES products(id),
    quantity INTEGER NOT NULL,
    price_cents INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 6. Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id SERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id UUID NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, product_id)
);