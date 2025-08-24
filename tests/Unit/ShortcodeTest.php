<?php
/**
 * Tests for the Shortcode class.
 *
 * @package BrightNucleus\Shortcode
 */

namespace BrightNucleus\Shortcode\Tests\Unit;

use BrightNucleus\Config\Config;
use BrightNucleus\Shortcode\Shortcode;
use BrightNucleus\Shortcode\ShortcodeAttsParser;
use BrightNucleus\Shortcode\ShortcodeInterface;
use BrightNucleus\View\ViewBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Test the Shortcode class.
 */
class ShortcodeTest extends TestCase {

	/**
	 * Test that a shortcode can be instantiated.
	 * 
	 * @covers \BrightNucleus\Shortcode\Shortcode::__construct
	 * @covers \BrightNucleus\Shortcode\Shortcode::get_tag
	 */
	public function test_shortcode_instantiation() {
		$config = new Config( [
			'view' => 'test-view',
			'atts' => [
				'test_att' => [
					'default' => 'default_value',
					'validate' => function( $value ) {
						return $value;
					},
				],
			],
		] );

		$atts_parser = new ShortcodeAttsParser( $config );

		$shortcode = new Shortcode(
			'test_shortcode',
			$config,
			$atts_parser
		);

		$this->assertInstanceOf( Shortcode::class, $shortcode );
		$this->assertInstanceOf( ShortcodeInterface::class, $shortcode );
		$this->assertEquals( 'test_shortcode', $shortcode->get_tag() );
	}

	/**
	 * Test that shortcode can add context.
	 * 
	 * @covers \BrightNucleus\Shortcode\Shortcode::add_context
	 */
	public function test_add_context() {
		$config = new Config( [ 'view' => 'test-view' ] );
		$atts_parser = new ShortcodeAttsParser( $config );
		$shortcode = new Shortcode( 'test_tag', $config, $atts_parser );

		$reflection = new \ReflectionClass( $shortcode );
		$context_property = $reflection->getProperty( 'context' );
		$context_property->setAccessible( true );

		$shortcode->add_context( [ 'key1' => 'value1' ] );
		$context = $context_property->getValue( $shortcode );
		$this->assertEquals( 'value1', $context['key1'] );

		$shortcode->add_context( [ 'key2' => 'value2' ] );
		$context = $context_property->getValue( $shortcode );
		$this->assertEquals( 'value1', $context['key1'] );
		$this->assertEquals( 'value2', $context['key2'] );
	}

	/**
	 * Test the do_this method.
	 * 
	 * @covers \BrightNucleus\Shortcode\Shortcode::do_this
	 */
	public function test_do_this() {
		global $shortcode_tags;
		$shortcode_tags = $shortcode_tags ?? [];

		$config = new Config( [ 'view' => 'test-view' ] );
		$atts_parser = new ShortcodeAttsParser( $config );
		$shortcode = new Shortcode( 'test_tag', $config, $atts_parser );

		$result = $shortcode->do_this( [ 'attr' => 'value' ], 'content' );
		$this->assertFalse( $result );
	}

	/**
	 * Test shortcode with dependencies configuration.
	 * 
	 * @covers \BrightNucleus\Shortcode\Shortcode::__construct
	 */
	public function test_shortcode_with_dependencies() {
		$config = new Config( [
			'view' => 'test-view',
			'dependencies' => [ 'jquery', 'custom-script' ]
		] );
		$atts_parser = new ShortcodeAttsParser( $config );
		$shortcode = new Shortcode( 'test_tag', $config, $atts_parser );

		$this->assertInstanceOf( Shortcode::class, $shortcode );
		$this->assertEquals( 'test_tag', $shortcode->get_tag() );
	}

	/**
	 * Test shortcode with custom ViewBuilder.
	 * 
	 * @covers \BrightNucleus\Shortcode\Shortcode::__construct
	 */
	public function test_shortcode_with_view_builder() {
		$config = new Config( [ 'view' => 'test-view' ] );
		$atts_parser = new ShortcodeAttsParser( $config );
		$view_builder = new ViewBuilder();
		$shortcode = new Shortcode( 'test_tag', $config, $atts_parser, null, $view_builder );

		$this->assertInstanceOf( Shortcode::class, $shortcode );
		$this->assertEquals( 'test_tag', $shortcode->get_tag() );
	}

	/**
	 * Test array syntax is modernized.
	 * 
	 * @covers \BrightNucleus\Shortcode\ShortcodeManager
	 */
	public function test_modern_array_syntax() {
		$file_content = file_get_contents( dirname( dirname( __DIR__ ) ) . '/src/ShortcodeManager.php' );
		
		$this->assertStringContainsString( 'protected $shortcodes = []', $file_content );
		$this->assertStringContainsString( 'protected $shortcode_uis = []', $file_content );
		
		$this->assertStringNotContainsString( 'protected $shortcodes = array()', $file_content );
		$this->assertStringNotContainsString( 'protected $shortcode_uis = array()', $file_content );
	}
}