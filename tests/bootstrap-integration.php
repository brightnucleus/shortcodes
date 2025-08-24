<?php
/**
 * PHPUnit bootstrap file for integration tests.
 * 
 * This file loads the WordPress test environment for integration testing.
 */

// Enable all error reporting including deprecations
error_reporting(E_ALL);

// Load composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Determine the WordPress test directory
// When using @wordpress/env, tests are mounted at /wordpress-phpunit
$wp_tests_dir = getenv('WP_TESTS_DIR');

if (!$wp_tests_dir) {
    // Default location when using @wordpress/env
    $wp_tests_dir = '/wordpress-phpunit';
    
    // Fallback to system-installed tests if wp-env path doesn't exist
    if (!file_exists($wp_tests_dir . '/includes/functions.php')) {
        // Try common locations
        $possible_paths = [
            '/tmp/wordpress-tests-lib',
            dirname(__DIR__, 4) . '/wordpress-tests-lib',
            dirname(__DIR__, 4) . '/wordpress-develop/tests/phpunit',
        ];
        
        foreach ($possible_paths as $path) {
            if (file_exists($path . '/includes/functions.php')) {
                $wp_tests_dir = $path;
                break;
            }
        }
    }
}

// Give access to tests_add_filter() function
if (file_exists($wp_tests_dir . '/includes/functions.php')) {
    include_once $wp_tests_dir . '/includes/functions.php';
} else {
    die("WordPress test suite not found. Please run 'npm install -g @wordpress/env && wp-env start' first.\n");
}

// Manually load the plugin for testing
function _manually_load_plugin()
{
    // Since this is a library, not a plugin, we just ensure autoloading works
    // The actual WordPress functionality is tested through the collections
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WordPress test suite
require $wp_tests_dir . '/includes/bootstrap.php';