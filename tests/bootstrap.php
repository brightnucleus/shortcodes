<?php
/**
 * PHPUnit bootstrap file.
 *
 * Enable all error reporting including deprecations
 * to catch PHP 8.1+ deprecation notices.
 */

// Enable all error reporting including deprecations
error_reporting(E_ALL);

// Load composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Mock WordPress functions for unit tests
if (!function_exists('shortcode_atts')) {
	/**
	 * Mock shortcode_atts function for unit tests.
	 *
	 * @param array  $pairs     Entire list of supported attributes and their defaults.
	 * @param array  $atts      User defined attributes in shortcode tag.
	 * @param string $shortcode Optional. The name of the shortcode, provided for context to enable filtering.
	 * @return array Combined and filtered attribute list.
	 */
	function shortcode_atts($pairs, $atts, $shortcode = '') {
		$atts = (array) $atts;
		$out = array();
		foreach ($pairs as $name => $default) {
			if (array_key_exists($name, $atts)) {
				$out[$name] = $atts[$name];
			} else {
				$out[$name] = $default;
			}
		}
		return $out;
	}
}
