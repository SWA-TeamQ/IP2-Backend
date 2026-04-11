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

}

?>