<?php
namespace App\Repositories;

use App\Entities\Product;
use PDO;

class ProductRepository extends BaseRepository {
    public function getAll(array $filters = []): array {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name ILIKE :search OR description ILIKE :search)";
            $params['search'] = "%" . $filters['search'] . "%";
        }

        $sortBy = $filters['sortBy'] ?? 'created_at';
        $sortOrder = $filters['sortOrder'] ?? 'DESC';
        $sql .= " ORDER BY $sortBy $sortOrder";

        $limit = (int)($filters['limit'] ?? 20);
        $page = (int)($filters['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        
        $sql .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->query($sql, $params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($data) => new Product($data), $results);
    }

    public function findByIdOrSlug(string $idOrSlug): ?Product {
        $sql = "SELECT * FROM products WHERE id::text = :val OR slug = :val";
        $stmt = $this->query($sql, ['val' => $idOrSlug]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Product($data) : null;
    }

    public function create(Product $product): string {
        $sql = "INSERT INTO products (name, slug, description, price_cents, sale_price_cents, images, category, badge, attributes, features, highlights, stock_quantity) 
                VALUES (:name, :slug, :description, :price_cents, :sale_price_cents, :images, :category, :badge, :attributes, :features, :highlights, :stock_quantity)
                RETURNING id";
        
        $stmt = $this->query($sql, [
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'price_cents' => $product->getPriceCents(),
            'sale_price_cents' => $product->getSalePriceCents(),
            'images' => $this->toPostgresArray($product->getImages()),
            'category' => $product->getCategory(),
            'badge' => $product->getBadge(),
            'attributes' => json_encode($product->getAttributes()),
            'features' => $this->toPostgresArray($product->getFeatures()),
            'highlights' => $this->toPostgresArray($product->getHighlights()),
            'stock_quantity' => $product->getStockQuantity()
        ]);
        
        return $stmt->fetchColumn();
    }

    public function update(string $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];

        $updatable = [
            'name', 'description', 'price_cents', 'sale_price_cents', 
            'category', 'badge', 'rating', 'review_count', 'stock_quantity'
        ];

        foreach ($updatable as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (isset($data['images'])) {
            $fields[] = "images = :images";
            $params['images'] = $this->toPostgresArray($data['images']);
        }
        if (isset($data['attributes'])) {
            $fields[] = "attributes = :attributes";
            $params['attributes'] = json_encode($data['attributes']);
        }

        if (empty($fields)) return false;

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->query($sql, $params);
        return true;
    }

    public function delete(string $id): void {
        $this->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    }
}