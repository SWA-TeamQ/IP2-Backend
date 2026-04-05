<?php

if (!function_exists('app_success_response')) {
	// Standard success payload for API responses.
	function app_success_response($data = array(), $meta = array())
	{
		return array(
			'success' => true,
			'data' => $data,
			'meta' => $meta
		);
	}
}

if (!function_exists('app_error_response')) {
	// Standard error payload for API responses.
	function app_error_response($code, $message, $details = array())
	{
		return array(
			'success' => false,
			'error' => array(
				'code' => $code,
				'message' => $message,
				'details' => $details
			)
		);
	}
}

if (!function_exists('app_format_money')) {
	// Keep money values readable in the UI.
	function app_format_money($amount, $currency = '', $decimals = 2)
	{
		$amount = is_numeric($amount) ? (float) $amount : 0;
		$formatted = number_format($amount, $decimals, '.', ',');

		return $currency . $formatted;
	}
}

if (!function_exists('app_format_date')) {
	// Format dates the same way across the app.
	function app_format_date($value, $format = 'Y-m-d H:i:s')
	{
		if (empty($value)) {
			return null;
		}

		$timestamp = is_numeric($value) ? (int) $value : strtotime($value);
		if ($timestamp === false) {
			return null;
		}

		return date($format, $timestamp);
	}
}

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

if (!function_exists('app_pick')) {
	// Small helper for safely reading array values.
	function app_pick($data, $key, $default = null)
	{
		return is_array($data) && array_key_exists($key, $data) ? $data[$key] : $default;
	}
}
