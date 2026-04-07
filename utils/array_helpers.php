<?php

if (!function_exists('app_pick')) {
	// Small helper for safely reading array values.
	function app_pick($data, $key, $fallback = null)
	{
		return is_array($data) && array_key_exists($key, $data) ? $data[$key] : $fallback;
	}
}
