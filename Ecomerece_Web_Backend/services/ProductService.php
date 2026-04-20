<?php
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/Product.php';

class ProductService{
    private $repo;

    public function __construct($db)
    {
        $this->repo = new ProductRepository($db);
    }

    public function getAllProducts(){
        $rows = $this->repo->getAll();
        return array_map(function($row){
            return new Product($row);},$rows);
        }

    public function getProductById($id){
        $data = $this->repo->getProductsById($id);
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function getFilteredProducts($category, $search, $sortBy, $order) {
        $query = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        if ($search) {
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $allowedSort = ['name', 'price', 'rating'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'name';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $query .= " ORDER BY $sortBy $order";
        
        $stmt = $this->repo->getDB()->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
        return new Product($row);
        }, $rows);
    }
    public function createProduct($data) {
    return $this->repo->create($data);
}

public function updateProduct($id, $data) {
    return $this->repo->update($id, $data);
}

public function deleteProduct($id) {
    return $this->repo->delete($id);
}

    }
?>




