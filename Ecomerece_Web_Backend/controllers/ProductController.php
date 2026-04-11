<?php
require_once __DIR__ . '/../services/ProductService.php';

class ProductController{
    private $service;

    public function __construct($db)
    {
        $this->service = new ProductService($db);
    }

    public function index(){
        $category = $_GET['category'] ?? null;
    
            if($category){
                $products = $this->service->getProductByCategory($category);
            }
            else{
                $products = $this->service->getAllProducts();
            }

            header('Content-Type: application/json');
            echo json_encode([
                "success" => true,
                "data" => ["items" => $products],
                "meta" => ["total" => count($products)]
            ]);
        }

        public function show($id){
            $product = $this->service->getProductById($id);

            header('Content-Type: application/json');
            if(!$product){
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "error" => ["code" => "Not_Found",
                    "message" => "Product not found by this id"]
                ]);
                return;
            }
            return json_encode([
                "success" => true,
                "data" => $product
            ]);
        }
}

?>