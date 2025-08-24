<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\Config;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Dependency\DependencyManagerInterface;
use PHPUnit\Framework\TestCase;

final class ShortcodeUITest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeUI::__construct
	 */
	public function test_it_can_be_instantiated() {
		$config      = $this->createMock( ConfigInterface::class );

		$shortcodeUI = new ShortcodeUI( 'some_tag', $config );

		$this->assertInstanceOf( ShortcodeUI::class, $shortcodeUI );
		$this->assertInstanceOf( ShortcodeUIInterface::class, $shortcodeUI );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeUI::__construct
	 */
	public function test_it_can_be_instantiated_with_dependencies() {
		$config = $this->createMock( ConfigInterface::class );
		$dependencies = $this->createMock( DependencyManagerInterface::class );

		$shortcodeUI = new ShortcodeUI( 'some_tag', $config, $dependencies );

		$this->assertInstanceOf( ShortcodeUI::class, $shortcodeUI );
		$this->assertInstanceOf( ShortcodeUIInterface::class, $shortcodeUI );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeUI::register
	 */
	public function test_register_with_needed_condition() {
		$config = new Config( [ 'is_needed' => true ] );
		$shortcodeUI = new ShortcodeUI( 'test_tag', $config );

		$this->expectException( \Error::class );
		$shortcodeUI->register();
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeUI::register
	 */
	public function test_register_with_not_needed_condition() {
		$config = new Config( [ 'is_needed' => false ] );
		$shortcodeUI = new ShortcodeUI( 'test_tag', $config );

		$shortcodeUI->register();
		$this->addToAssertionCount( 1 );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeUI::register
	 */
	public function test_register_with_callable_condition() {
		$config = new Config( [ 'is_needed' => fn() => false ] );
		$shortcodeUI = new ShortcodeUI( 'test_tag', $config );

		$shortcodeUI->register();
		$this->addToAssertionCount( 1 );
	}
}
