<?php
// Define base paths to avoid repeating __DIR__ . '/..'
$basePath = realpath(__DIR__ . '/../');
$rootBasePath = realpath(__DIR__ . '/../..');

require_once $basePath . '/repositories/ProductRepository.php';
require_once $basePath . '/middleware/AuthMiddleware.php';
require_once $rootBasePath . '/utils/request.php';
require_once $rootBasePath . '/utils/responses.php';

class ProductController
{
    private $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    private function jsonResponse($payload, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($payload);
    }

    private function validateProductPayload($data, $isPartial = false)
    {
        $errors = array();

        if (!$isPartial || array_key_exists('name', $data)) {
            $name = isset($data['name']) ? trim((string) $data['name']) : '';
            if ($name === '') {
                $errors['name'] = 'Name is required.';
            }
        }

        if (!$isPartial || array_key_exists('price', $data)) {
            if (!isset($data['price']) || !is_numeric($data['price']) || (float) $data['price'] < 0) {
                $errors['price'] = 'Price must be a non-negative number.';
            }
        }

        if (array_key_exists('salePrice', $data) && $data['salePrice'] !== null && $data['salePrice'] !== '') {
            if (!is_numeric($data['salePrice']) || (float) $data['salePrice'] < 0) {
                $errors['salePrice'] = 'salePrice must be a non-negative number.';
            }
        }

        if (array_key_exists('stock', $data)) {
            if (!is_numeric($data['stock']) || (int) $data['stock'] < 0) {
                $errors['stock'] = 'Stock must be a non-negative integer.';
            }
        }

        if (array_key_exists('rating', $data) && $data['rating'] !== null && $data['rating'] !== '') {
            if (!is_numeric($data['rating']) || (float) $data['rating'] < 0) {
                $errors['rating'] = 'Rating must be a non-negative number.';
            }
        }

        if (array_key_exists('images', $data) && $data['images'] !== null && !is_array($data['images']) && !is_string($data['images'])) {
            $errors['images'] = 'Images must be an array or string.';
        }

        return $errors;
    }

    public function listProducts()
    {
        try {
            $filters = array(
                'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : null,
                'search' => isset($_GET['search']) ? trim((string) $_GET['search']) : null,
                'category' => isset($_GET['category']) ? trim((string) $_GET['category']) : null,
                'sortBy' => isset($_GET['sortBy']) ? trim((string) $_GET['sortBy']) : 'name',
                'order' => isset($_GET['order']) ? trim((string) $_GET['order']) : 'asc',
                'page' => isset($_GET['page']) ? (int) $_GET['page'] : 1,
                'limit' => isset($_GET['limit']) ? (int) $_GET['limit'] : 12
            );

            $result = $this->productRepository->findFiltered($filters);

            $items = array();
            foreach ($result['items'] as $product) {
                $items[] = $product->toArray();
            }

            $this->jsonResponse(
                app_success_response(
                    array('items' => $items),
                    array(
                        'page' => $result['page'],
                        'limit' => $result['limit'],
                        'total' => $result['total']
                    )
                )
            );
        } catch (Throwable $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'Could not load products'),
                500
            );
        }
    }

    public function getProduct($id)
    {
        try {
            $product = $this->productRepository->getProductById((int) $id);

            if (!$product) {
                $this->jsonResponse(app_error_response('NOT_FOUND', 'Product not found'), 404);
                return;
            }

            $this->jsonResponse(app_success_response($product->toArray()));
        } catch (Throwable $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'Could not load product'),
                500
            );
        }
    }

    public function createProduct()
    {
        AuthMiddleware::isAdmin();

        try {
            $data = app_get_request_body();
            $errors = $this->validateProductPayload($data, false);

            if (!empty($errors)) {
                $this->jsonResponse(
                    app_error_response('VALIDATION_ERROR', 'Invalid product payload', $errors),
                    400
                );
                return;
            }

            $id = $this->productRepository->create($data);
            if (!$id) {
                $this->jsonResponse(
                    app_error_response('CREATE_FAILED', 'Could not create product'),
                    500
                );
                return;
            }

            $product = $this->productRepository->getProductById($id);
            $this->jsonResponse(app_success_response(array('item' => $product->toArray())), 201);
        } catch (Throwable $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'Could not create product'),
                500
            );
        }
    }

    public function updateProduct($id)
    {
        AuthMiddleware::isAdmin();

        try {
            $existing = $this->productRepository->getProductById((int) $id);
            if (!$existing) {
                $this->jsonResponse(app_error_response('NOT_FOUND', 'Product not found'), 404);
                return;
            }

            $data = app_get_request_body();
            $errors = $this->validateProductPayload($data, true);

            if (!empty($errors)) {
                $this->jsonResponse(
                    app_error_response('VALIDATION_ERROR', 'Invalid product payload', $errors),
                    400
                );
                return;
            }

            $updated = $this->productRepository->update((int) $id, $data);
            if (!$updated) {
                $this->jsonResponse(
                    app_error_response('UPDATE_FAILED', 'Could not update product'),
                    500
                );
                return;
            }

            $product = $this->productRepository->getProductById((int) $id);
            $this->jsonResponse(app_success_response(array('item' => $product->toArray())));
        } catch (Throwable $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'Could not update product'),
                500
            );
        }
    }

    public function deleteProduct($id)
    {
        AuthMiddleware::isAdmin();

        try {
            $existing = $this->productRepository->getProductById((int) $id);
            if (!$existing) {
                $this->jsonResponse(app_error_response('NOT_FOUND', 'Product not found'), 404);
                return;
            }

            $deleted = $this->productRepository->delete((int) $id);
            if (!$deleted) {
                $this->jsonResponse(
                    app_error_response('DELETE_FAILED', 'Could not delete product'),
                    500
                );
                return;
            }

            $this->jsonResponse(app_success_response(array('deleted' => true)));
        } catch (Throwable $e) {
            $this->jsonResponse(
                app_error_response('INTERNAL_SERVER_ERROR', 'Could not delete product'),
                500
            );
        }
    }

    // Compatibility aliases for older code paths.
    public function index()
    {
        $this->listProducts();
    }

    public function show($id)
    {
        $this->getProduct($id);
    }

    public function store()
    {
        $this->createProduct();
    }

    public function update($id)
    {
        $this->updateProduct($id);
    }

    public function destroy($id)
    {
        $this->deleteProduct($id);
    }
}
