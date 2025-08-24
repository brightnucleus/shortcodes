<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\Config;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Dependency\DependencyManagerInterface;
use BrightNucleus\View\ViewBuilder;
use PHPUnit\Framework\TestCase;

final class ShortcodeManagerTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::__construct
	 */
	public function test_it_can_be_instantiated() {
		$config = $this->createMock( ConfigInterface::class );

		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config );

		$this->assertInstanceOf( ShortcodeManager::class, $shortcodeManager );
		$this->assertInstanceOf(
			ShortcodeManagerInterface::class,
			$shortcodeManager
		);
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::__construct
	 */
	public function test_it_can_be_instantiated_with_dependencies() {
		$config = $this->createMock( ConfigInterface::class );
		$dependencies = $this->createMock( DependencyManagerInterface::class );

		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config, $dependencies );

		$this->assertInstanceOf( ShortcodeManager::class, $shortcodeManager );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::__construct
	 */
	public function test_it_can_be_instantiated_with_view_builder() {
		$config = $this->createMock( ConfigInterface::class );
		$viewBuilder = new ViewBuilder();

		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config, null, $viewBuilder );

		$this->assertInstanceOf( ShortcodeManager::class, $shortcodeManager );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::with_injector
	 */
	public function test_with_injector() {
		$config = $this->createMock( ConfigInterface::class );
		$injector = new class {
			public function make( $class, $args = [] ) {
				return new $class( ...$args );
			}
		};

		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config );
		$shortcodeManager->with_injector( $injector );

		$this->assertInstanceOf( ShortcodeManager::class, $shortcodeManager );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::with_injector
	 */
	public function test_with_injector_throws_exception_for_invalid_injector() {
		$config = $this->createMock( ConfigInterface::class );
		$injector = new class {};

		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config );

		$this->expectException( \BrightNucleus\Exception\RuntimeException::class );
		$this->expectExceptionMessage( 'Invalid injector provided, it does not have a make() method.' );

		$shortcodeManager->with_injector( $injector );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager::do_tag
	 */
	public function test_do_tag() {
		global $shortcode_tags;
		$shortcode_tags = $shortcode_tags ?? [];

		$config = $this->createMock( ConfigInterface::class );
		$config->method( 'getKeys' )->willReturn( [] );

		$shortcodeManager = new ShortcodeManager( $config );
		$result = $shortcodeManager->do_tag( 'nonexistent_tag', [ 'attr' => 'value' ] );

		$this->assertFalse( $result );
	}
}
