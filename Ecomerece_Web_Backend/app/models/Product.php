<?php
namespace App\Models;

class Product extends Model {
    public function getAll($filters = []) {
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

        $limit = $filters['limit'] ?? 20;
        $page = $filters['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        
        $sql .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findByIdOrSlug($idOrSlug) {
        $sql = "SELECT * FROM products WHERE id::text = :val OR slug = :val";
        return $this->query($sql, ['val' => $idOrSlug])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO products (name, slug, description, price_cents, sale_price_cents, images, category, badge, attributes, features, highlights) 
                VALUES (:name, :slug, :description, :price_cents, :sale_price_cents, :images, :category, :badge, :attributes, :features, :highlights)
                RETURNING id";
        
        return $this->query($sql, [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? $this->generateSlug($data['name']),
            'description' => $data['description'],
            'price_cents' => $data['price_cents'],
            'sale_price_cents' => $data['sale_price_cents'] ?? null,
            'images' => $this->toPostgresArray($data['images']),
            'category' => $data['category'],
            'badge' => $data['badge'] ?? null,
            'attributes' => json_encode($data['attributes']),
            'features' => $this->toPostgresArray($data['features']),
            'highlights' => $this->toPostgresArray($data['highlights'])
        ])->fetchColumn();
    }

    public function update($id, $data) {
        // Implementation for partial updates could go here
    }

    public function delete($id) {
        return $this->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    }

    private function generateSlug($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    private function toPostgresArray($phpArray) {
        if (!is_array($phpArray)) return '{}';
        $result = [];
        foreach ($phpArray as $item) {
            $result[] = '"' . str_replace('"', '\\"', $item) . '"';
        }
        return '{' . implode(',', $result) . '}';
    }
}