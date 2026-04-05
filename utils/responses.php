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
