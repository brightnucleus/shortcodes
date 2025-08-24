<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\Config;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Dependency\DependencyManagerInterface;
use PHPUnit\Framework\TestCase;

final class TemplatedShortcodeTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\TemplatedShortcode::__construct
	 */
	public function test_it_can_be_instantiated() {
		$config     = $this->createMock( ConfigInterface::class );
		$attsParser = $this->createMock( ShortcodeAttsParserInterface::class );

		$shortcode  = new TemplatedShortcode(
			'some_tag',
			$config,
			$attsParser
		);

		$this->assertInstanceOf( TemplatedShortcode::class, $shortcode );
		$this->assertInstanceOf( ShortcodeInterface::class, $shortcode );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\TemplatedShortcode::__construct
	 */
	public function test_it_can_be_instantiated_with_dependencies() {
		$config = $this->createMock( ConfigInterface::class );
		$attsParser = $this->createMock( ShortcodeAttsParserInterface::class );
		$dependencies = $this->createMock( DependencyManagerInterface::class );

		$shortcode = new TemplatedShortcode(
			'some_tag',
			$config,
			$attsParser,
			$dependencies
		);

		$this->assertInstanceOf( TemplatedShortcode::class, $shortcode );
		$this->assertInstanceOf( ShortcodeInterface::class, $shortcode );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\TemplatedShortcode::init_template_loader
	 */
	public function test_init_template_loader() {
		$config = new Config( [] );
		$attsParser = $this->createMock( ShortcodeAttsParserInterface::class );

		$shortcode = new TemplatedShortcode( 'test_tag', $config, $attsParser );
		$loader = $shortcode->init_template_loader();

		$this->assertInstanceOf( 'BrightNucleus\\Shortcode\\ShortcodeTemplateLoader', $loader );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\TemplatedShortcode::init_template_loader
	 */
	public function test_init_template_loader_with_custom_config() {
		$config = new Config( [
			'template' => [
				'custom_loader' => 'BrightNucleus\\Shortcode\\ShortcodeTemplateLoader',
				'filter_prefix' => 'custom_prefix',
				'template_directory' => 'custom_templates',
			],
			'view' => 'custom/view'
		] );
		$attsParser = $this->createMock( ShortcodeAttsParserInterface::class );

		$shortcode = new TemplatedShortcode( 'test_tag', $config, $attsParser );
		$loader = $shortcode->init_template_loader();

		$this->assertInstanceOf( 'BrightNucleus\\Shortcode\\ShortcodeTemplateLoader', $loader );
	}
}
