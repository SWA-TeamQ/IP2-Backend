<?php

require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../../utils/responses.php';

class ProductionController
{
	private $productRepository;

	public function __construct()
	{
		$this->productRepository = new ProductRepository(db());
	}
	private function jsonResponse($payload, $statusCode = 200)
	{
		http_response_code($statusCode);
		echo json_encode($payload);
	}

	public function listProducts()
	{
		try {
			$category = isset($_GET['category']) ? trim((string) $_GET['category']) : null;
			$name = isset($_GET['name']) ? trim((string) $_GET['name']) : null;

			if ($category !== null && $category !== '') {
				$products = $this->productRepository->getProductsByCategory($category);
			} elseif ($name !== null && $name !== '') {
				$products = $this->productRepository->getProductsByName($name);
			} else {
				$products = $this->productRepository->getAllProducts();
			}

			$items = array();
			foreach ($products as $product) {
				$items[] = $product->toArray();
			}

			$this->jsonResponse(
				app_success_response(
					array('items' => $items),
					array('total' => count($items))
				)
			);
		} catch (Throwable $e) {
			$this->jsonResponse(app_error_response('INTERNAL_SERVER_ERROR', 'Could not load products'), 500);
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
			$this->jsonResponse(app_error_response('INTERNAL_SERVER_ERROR', 'Could not load product'), 500);
		}
	}
}
