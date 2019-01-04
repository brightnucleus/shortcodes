<?php

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;

class BootstrapListener extends BaseTestListener {

	public function startTestSuite( TestSuite $suite ) {
		switch ( $suite->getName() ) {
			case 'integration':
				$_tests_dir = getenv( 'WP_TESTS_DIR' );

				if ( ! $_tests_dir ) {
					$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
				}

				if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
					echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?\n"; // WPCS: XSS ok.
					exit( 1 );
				}

				// As we had already included the 'testcase.php' file, we need
				// a quite hacky way of requiring the WordPress test suite
				// without throwing errors. Blame WordPress!
				$bootstrap = file_get_contents( $_tests_dir . '/includes/bootstrap.php' );
				$bootstrap = preg_replace( '/^<\?php\s*?$/m', '', $bootstrap );
				$bootstrap = preg_replace( '~^\s*?require\s*?dirname\( __FILE__ \) \. \'/testcase\.php\';\s*?$~m', '', $bootstrap );
				$bootstrap = str_replace( '__FILE__', "'{$_tests_dir}/includes/bootstrap.php'", $bootstrap );
				eval( $bootstrap );
				break;
			case 'unit':
			default:
				break;
		}
	}
}
