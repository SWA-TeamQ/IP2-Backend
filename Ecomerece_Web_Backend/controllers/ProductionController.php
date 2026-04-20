<?php

require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../../utils/responses.php';

class ProductionController
{
	private $productRepository;

	public function __construct()
	{
		$this->productRepository = new ProductRepository();
	}

	private function jsonResponse($payload, $statusCode = 200)
	{
		http_response_code($statusCode);
		echo json_encode($payload);
	}

	public function listProducts()
	{
		$products = $this->productRepository->getAllProducts();
		$items = array();

		foreach ($products as $product) {
			$items[] = $product->toArray();
		}

		$this->jsonResponse(
			app_success_response(
				array('items' => $items),
				array(
					'total' => count($items)
				)
			)
		);
	}

	public function getProduct($id)
	{
		$product = $this->productRepository->getProductById((int) $id);

		if (!$product) {
			$this->jsonResponse(app_error_response('NOT_FOUND', 'Product not found'), 404);
			return;
		}

		$this->jsonResponse(app_success_response($product->toArray()));
	}
}
