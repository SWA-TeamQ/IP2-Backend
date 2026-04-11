<?php 

class Product{
    public $id;
    public $name;
    public $description;
    public $category;
    public $price;
    public $sale_price;
    public $images;

    public function __construct($data = []){
        $this->id = $data['id'] ?? null;
         $this->name = $data['name'] ?? "";
          $this->description = $data['description'] ?? "";
        $this -> category = $data['category'] ?? "";
        $this-> price = $data['price'] ?? 0;
         $this->sale_price = $data['sell_price'] ?? null;
         $this->images = isset($data['images'])?json_decode($data['images']):[];
    }

}

?>