<?php

class ProductRepository {

    private $db;



    public function __construct($db) {

        $this->db = $db;

    }



    public function getAll() {

        $stmt = $this->db->prepare("SELECT * FROM products");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getProductsById($id){

        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=?");

        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getProductsByName($name){

        $stmt = $this->db->prepare("SELECT * FROM products WHERE name = ?");

        $stmt->execute([$name]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getProductsByCategory($category){

        $stmt = $this->db->prepare("SELECT * FROM products WHERE category=?");

        $stmt->execute([$category]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function update($id, $data) {
    $stmt = $this->db->prepare("UPDATE products SET name=?, description=?, category=?, price=?, sell_price=? WHERE id=?");
    return $stmt->execute([
        $data['name'], 
        $data['description'], 
        $data['category'], 
        $data['price'], 
        $data['sell_price'], 
        $id
    ]);
}

    public function delete($id) {
    $stmt = $this->db->prepare("DELETE FROM products WHERE id=?");
    return $stmt->execute([$id]);
}

public function create($data) {
    $stmt = $this->db->prepare("INSERT INTO products (name, description, category, price, sell_price, images) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['name'], 
        $data['description'], 
        $data['category'], 
        $data['price'], 
        $data['sell_price'] ?? null,
        $data['images'] ?? null
    ]);
}

}

?>