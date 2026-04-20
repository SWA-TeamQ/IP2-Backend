<?php

if (!function_exists('app_get_request_body')) {
	// Read JSON request bodies in one place.
	function app_get_request_body()
	{
		$rawBody = file_get_contents('php://input');
		if (!$rawBody) {
			return array();
		}

		$decoded = json_decode($rawBody, true);
		return is_array($decoded) ? $decoded : array();
	}
}
