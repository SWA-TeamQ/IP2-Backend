<?php
namespace App\Models;

class Product extends Model {
    public function getAll() {
        return $this->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
    }

    public function find($id) {
        return $this->query("SELECT * FROM products WHERE id = ?", [$id])->fetch();
    }
    public function search($term) {
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
    return $this->query($sql, ["%$term%", "%$term%"])->fetchAll();
    }
    public function updateStock($id, $newQuantity) {
        return $this->query("UPDATE products SET stock_quantity = ? WHERE id = ?", [$newQuantity, $id]);
    }
    public function create($data) {
        $sql = "INSERT INTO products (name, description, price, stock_quantity) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [
            $data['name'], 
            $data['description'], 
            $data['price'], 
            $data['stock_quantity']
        ]);
    }
    public function update($id, $data) {
        return $this->query("UPDATE products SET name = ?, price = ?, stock_quantity = ? WHERE id = ?", 
            [$data['name'], $data['price'], $data['stock_quantity'], $id]);
    }

    public function delete($id) {
        return $this->query("DELETE FROM products WHERE id = ?", [$id]);
    }
}