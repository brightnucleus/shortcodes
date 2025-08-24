<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\Config;
use PHPUnit\Framework\TestCase;

final class CheckNeedTraitTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\CheckNeedTrait::is_needed
	 */
	public function test_is_needed_returns_true_by_default() {
		$object = new class {
			use CheckNeedTrait {
				is_needed as public;
			}
			
			private $config;
			
			public function __construct() {
				$this->config = new Config( [] );
			}
			
			protected function hasConfigKey( $_ ) {
				return $this->config->hasKey( $_ );
			}
			
			protected function getConfigKey( $_ ) {
				return $this->config->getKey( $_ );
			}
		};

		$this->assertTrue( $object->is_needed() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\CheckNeedTrait::is_needed
	 */
	public function test_is_needed_returns_configured_boolean_value() {
		$objectClass = new class( false ) {
			use CheckNeedTrait {
				is_needed as public;
			}
			
			private $config;
			
			public function __construct( $needed ) {
				$this->config = new Config( [ 'is_needed' => $needed ] );
			}
			
			protected function hasConfigKey( $_ ) {
				return $this->config->hasKey( $_ );
			}
			
			protected function getConfigKey( $_ ) {
				return $this->config->getKey( $_ );
			}
		};

		$objectFalse = new $objectClass( false );
		$objectTrue = new $objectClass( true );

		$this->assertFalse( $objectFalse->is_needed() );
		$this->assertTrue( $objectTrue->is_needed() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\CheckNeedTrait::is_needed
	 */
	public function test_is_needed_calls_callable_with_context() {
		$context = [ 'page' => 'home' ];
		$callable = function( $ctx ) use ( $context ) {
			return $ctx === $context;
		};

		$objectClass = new class( $callable ) {
			use CheckNeedTrait {
				is_needed as public;
			}
			
			private $config;
			
			public function __construct( $callable ) {
				$this->config = new Config( [ 'is_needed' => $callable ] );
			}
			
			protected function hasConfigKey( $_ ) {
				return $this->config->hasKey( $_ );
			}
			
			protected function getConfigKey( $_ ) {
				return $this->config->getKey( $_ );
			}
		};

		$object = new $objectClass( $callable );

		$this->assertTrue( $object->is_needed( $context ) );
		$this->assertFalse( $object->is_needed( [ 'page' => 'about' ] ) );
	}
}