<?php

if (!function_exists('app_upload_product_image')) {
	// Upload one product image and return the public path.
	function app_upload_product_image($file, $options = array())
	{
		if (!is_array($file) || !isset($file['tmp_name']) || !isset($file['name'])) {
			throw new InvalidArgumentException('No valid image file was provided.');
		}

		if (!empty($file['error'])) {
			throw new RuntimeException('Image upload failed with error code ' . $file['error'] . '.');
		}

		$maxSize = isset($options['maxSize']) ? (int) $options['maxSize'] : 5 * 1024 * 1024;
		if (isset($file['size']) && (int) $file['size'] > $maxSize) {
			throw new RuntimeException('Image is too large. Max size is 5MB.');
		}

		$allowedExtensions = isset($options['allowedExtensions'])
			? $options['allowedExtensions']
			: array('jpg', 'jpeg', 'png', 'webp');

		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if (!in_array($extension, $allowedExtensions, true)) {
			throw new RuntimeException('Unsupported image type. Allowed: jpg, jpeg, png, webp.');
		}

		$baseDir = isset($options['baseDir'])
			? rtrim($options['baseDir'], '/\\')
			: dirname(__DIR__) . '/Ecomerece_Web_Backend/public/uploads/products';

		if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
			throw new RuntimeException('Could not create upload directory.');
		}

		$filename = uniqid('product_', true) . '.' . $extension;
		$targetPath = $baseDir . '/' . $filename;

		if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
			throw new RuntimeException('Could not move uploaded image.');
		}

		return '/uploads/products/' . $filename;
	}
}

if (!function_exists('app_upload_product_images')) {
	// Upload many product images from a multi-file input.
	function app_upload_product_images($files, $options = array())
	{
		$uploaded = array();

		if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
			return $uploaded;
		}

		$total = count($files['name']);
		for ($i = 0; $i < $total; $i++) {
			$singleFile = array(
				'name' => isset($files['name'][$i]) ? $files['name'][$i] : null,
				'type' => isset($files['type'][$i]) ? $files['type'][$i] : null,
				'tmp_name' => isset($files['tmp_name'][$i]) ? $files['tmp_name'][$i] : null,
				'error' => isset($files['error'][$i]) ? $files['error'][$i] : UPLOAD_ERR_NO_FILE,
				'size' => isset($files['size'][$i]) ? $files['size'][$i] : 0
			);

			if ((int) $singleFile['error'] === UPLOAD_ERR_NO_FILE) {
				continue;
			}

			$uploaded[] = app_upload_product_image($singleFile, $options);
		}

		return $uploaded;
	}
}
