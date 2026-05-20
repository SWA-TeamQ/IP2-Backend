CREATE TABLE IF NOT EXISTS orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    subtotal_cents INTEGER NOT NULL,
    tax_cents INTEGER NOT NULL,
    shipping_cents INTEGER NOT NULL,
    total_cents INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    shipping_address JSONB NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);