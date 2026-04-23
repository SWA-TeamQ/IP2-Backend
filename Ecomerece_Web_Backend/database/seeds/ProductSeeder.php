<?php
namespace Database\Seeds;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Env;
use App\Models\Product;

class ProductSeeder {
    public function run() {
        Env::load(__DIR__ . '/../../.env');
        $productModel = new Product();

        $products = [
            [
                'name' => 'Classic White Tee',
                'description' => 'A premium cotton white t-shirt perfectly tailored for everyday comfort.',
                'price_cents' => 2500,
                'sale_price_cents' => 1999,
                'category' => 'apparel',
                'badge' => 'Sale',
                'images' => [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&q=80&w=800'
                ],
                'attributes' => [
                    ['name' => 'Color', 'value' => 'White'],
                    ['name' => 'Material', 'value' => '100% Cotton']
                ],
                'features' => ['Breathable fabric', 'Pre-shrunk', 'Relaxes fit'],
                'highlights' => ['Classic design', 'Durable stitching']
            ],
            [
                'name' => 'Tech Backpack v2',
                'description' => 'Water-resistant backpack with dedicated compartments for 16-inch laptops and accessories.',
                'price_cents' => 8500,
                'category' => 'accessories',
                'badge' => 'Popular',
                'images' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&q=80&w=800'
                ],
                'attributes' => [
                    ['name' => 'Capacity', 'value' => '25L'],
                    ['name' => 'Material', 'value' => 'Ballistic Nylon']
                ],
                'features' => ['Hidden pocket', 'USB charging port', 'Padded straps'],
                'highlights' => ['Lightweight', 'Anti-theft zipper']
            ],
            [
                'name' => 'Luxe Leather Wallet',
                'description' => 'Handcrafted top-grain leather wallet with RFID blocking technology.',
                'price_cents' => 4500,
                'category' => 'accessories',
                'badge' => 'New',
                'images' => [
                    'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=800'
                ],
                'attributes' => [
                    ['name' => 'Color', 'value' => 'Cognac'],
                    ['name' => 'Leather Type', 'value' => 'Full Grain']
                ],
                'features' => ['6 card slots', 'Bill compartment', 'Slim profile'],
                'highlights' => ['Premium gift box', 'RFID security']
            ]
        ];

        foreach ($products as $p) {
            try {
                $id = $productModel->create($p);
                echo "Created product: {$p['name']} (ID: $id)\n";
            } catch (\Exception $e) {
                echo "Failed to create product {$p['name']}: " . $e->getMessage() . "\n";
            }
        }
    }
}

$seeder = new ProductSeeder();
$seeder->run();