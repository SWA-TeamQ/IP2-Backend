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