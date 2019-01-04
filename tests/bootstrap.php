<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?\n"; // WPCS: XSS ok.
	exit( 1 );
}

if ( class_exists( 'PHPUnit\Runner\Version' ) ) {
	require_once $_tests_dir . '/includes/phpunit6-compat.php';
}
require_once $_tests_dir . '/includes/testcase.php';
