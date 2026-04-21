<?php 

class Product implements JsonSerializable{
    public $id;
    public $name;
    public $description;
    public $category;
    public $price;
    public $sale_price;
    public $images;
    public $features = [];
    public $highlights = [];

    public function __construct($data = []){
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? "";
        $this->description = $data['description'] ?? "";
        $this -> category = $data['category'] ?? "";
        $this-> price = $data['price'] ?? 0;
        $this->sale_price = $data['sell_price'] ?? null;
        $this->images = isset($data['images'])?json_decode($data['images']):[];
        $this->features = isset($data['features']) ? json_decode($data['features'], true) : [];
        $this->highlights = isset($data['highlights']) ? json_decode($data['highlights'], true) : [];
        }

    public function jsonSerialize(): mixed {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "price" => (float)$this->price,
            "salePrice" => (float)$this->sale_price,
            "images" => $this->images,
            "details" => [
                "category" => $this->category,
                "rating" => 4.6, 
                "badge" => "NEW",
                "color" => "Silver",
                "reviewCount" => 0
            ],
            "features" => $this->features,
            "highlights" => $this->highlights,
            "createdAt" => date('c'), 
            "updatedAt" => date('c')
        ];
    }

}

?>