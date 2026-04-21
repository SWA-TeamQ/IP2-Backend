<?php

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
