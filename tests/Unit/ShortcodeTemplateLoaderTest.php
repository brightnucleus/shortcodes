<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use PHPUnit\Framework\TestCase;

final class ShortcodeTemplateLoaderTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeTemplateLoader::__construct
	 */
	public function test_it_can_be_instantiated() {
		$loader = new ShortcodeTemplateLoader(
			'test_prefix',
			'test_template_dir',
			'test_view_dir'
		);

		$this->assertInstanceOf( ShortcodeTemplateLoader::class, $loader );
		$this->assertInstanceOf( 'Gamajo_Template_Loader', $loader );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeTemplateLoader::get_templates_dir
	 */
	public function test_get_templates_dir_returns_view_directory() {
		$loader = new ShortcodeTemplateLoader(
			'test_prefix',
			'test_template_dir',
			'test_view_dir'
		);

		$reflection = new \ReflectionClass( $loader );
		$method = $reflection->getMethod( 'get_templates_dir' );
		$method->setAccessible( true );

		$this->assertEquals( 'test_view_dir', $method->invoke( $loader ) );
	}
}