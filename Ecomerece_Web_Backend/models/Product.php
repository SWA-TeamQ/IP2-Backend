<?php 

class Product implements JsonSerializable {
    public $id;
    public $name;
    public $description;
    public $category;
    public $price;
    public $sale_price;
    public $images;
    public $features;
    public $highlights;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? "";
        $this->description = $data['description'] ?? "";
        $this->category = $data['category'] ?? "";
        $this->price = $data['price'] ?? 0;
        $this->sale_price = $data['sell_price'] ?? $data['sale_price'] ?? null;
        
        // Use the robust decoding logic from Product.model.php
        $rawImages = $data['images'] ?? null;
        if (is_string($rawImages)) {
            $decoded = json_decode($rawImages, true);
            $this->images = is_array($decoded) ? $decoded : ($rawImages !== '' ? [$rawImages] : []);
        } else {
            $this->images = is_array($rawImages) ? $rawImages : [];
        }

        $this->features = isset($data['features']) ? (is_string($data['features']) ? json_decode($data['features'], true) : $data['features']) : [];
        $this->highlights = isset($data['highlights']) ? (is_string($data['highlights']) ? json_decode($data['highlights'], true) : $data['highlights']) : [];
    }

    public function jsonSerialize(): mixed {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "price" => (float)$this->price,
            "salePrice" => $this->sale_price ? (float)$this->sale_price : null,
            "images" => $this->images,
            "details" => [
                "category" => $this->category,
                "rating" => 4.6, // Keep existing placeholder or map from DB
                "badge" => "NEW"
            ],
            "features" => $this->features,
            "highlights" => $this->highlights
        ];
    }
}