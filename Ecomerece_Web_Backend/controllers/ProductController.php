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
            $search = $_GET['search'] ?? null;
            $sortBy = $_GET['sortBy'] ?? 'name';
            $order = $_GET['order'] ?? 'asc';

            // Pass all contract query params to the service
            $products = $this->service->getFilteredProducts($category, $search, $sortBy, $order);

            header('Content-Type: application/json');
            echo json_encode(["items" => $products]);
        }

        public function show($id){
            $product = $this->service->getProductById($id);
            header('Content-Type: application/json');
            if(!$product){
                $this->sendError("NOT_FOUND", "Product not found by this id", 404);
                    return;
            }
            echo json_encode(["item" => $product]);
        }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $this->service->createProduct($data); // Ensure repo returns lastInsertId

            if ($id) {
                $newProduct = $this->service->getProductById($id);
                echo json_encode(["item" => $newProduct]); //
            } else {
                $this->sendError("CREATE_FAILED", "Could not create product");
            }
}

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->service->updateProduct($id, $data);

        header('Content-Type: application/json');
if ($result) {
        $updatedProduct = $this->service->getProductById($id);
        echo json_encode(["item" => $updatedProduct]);
    } else {
        $this->sendError("UPDATE_FAILED", "Could not update product");
    }
    }

    public function destroy($id) {
        $result = $this->service->deleteProduct($id);

        header('Content-Type: application/json');
        echo json_encode(["ok" => (bool)$result]);
}

    private function sendError($code, $message, $statusCode = 400) {
        http_response_code($statusCode);
        echo json_encode([
            "error" => [
                "code" => $code,
                "message" => $message,
                "details" => (object)[]
            ]
        ]);
        exit;
    }

}

?>