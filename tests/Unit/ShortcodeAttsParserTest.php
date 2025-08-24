<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\Config;
use BrightNucleus\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

final class ShortcodeAttsParserTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeAttsParser::__construct
	 */
	public function test_it_can_be_instantiated() {
		$config     = $this->createMock( ConfigInterface::class );

		$attsParser = new ShortcodeAttsParser( $config );

		$this->assertInstanceOf( ShortcodeAttsParser::class, $attsParser );
		$this->assertInstanceOf(
			ShortcodeAttsParserInterface::class,
			$attsParser
		);
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeAttsParser::parse_atts
	 */
	public function test_parse_atts_with_valid_attributes() {
		$config = new Config( [
			'atts' => [
				'color' => [
					'default' => 'blue',
					'validate' => fn( $value ) => in_array( $value, [ 'red', 'green', 'blue' ], true ) ? $value : 'blue',
				],
				'size' => [
					'default' => 'medium',
					'validate' => fn( $value ) => in_array( $value, [ 'small', 'medium', 'large' ], true ) ? $value : 'medium',
				],
			],
		] );

		$parser = new ShortcodeAttsParser( $config );

		$atts = $parser->parse_atts( [ 'color' => 'red', 'size' => 'large' ], 'test' );
		$this->assertEquals( 'red', $atts['color'] );
		$this->assertEquals( 'large', $atts['size'] );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeAttsParser::parse_atts
	 */
	public function test_parse_atts_with_invalid_attributes() {
		$config = new Config( [
			'atts' => [
				'color' => [
					'default' => 'blue',
					'validate' => fn( $value ) => in_array( $value, [ 'red', 'green', 'blue' ], true ) ? $value : 'blue',
				],
				'size' => [
					'default' => 'medium',
					'validate' => fn( $value ) => in_array( $value, [ 'small', 'medium', 'large' ], true ) ? $value : 'medium',
				],
			],
		] );

		$parser = new ShortcodeAttsParser( $config );

		$atts = $parser->parse_atts( [ 'color' => 'yellow' ], 'test' );
		$this->assertEquals( 'blue', $atts['color'] );
		$this->assertEquals( 'medium', $atts['size'] );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeAttsParser::parse_atts
	 */
	public function test_parse_atts_with_no_attributes() {
		$config = new Config( [
			'atts' => [
				'color' => [
					'default' => 'blue',
					'validate' => fn( $value ) => $value,
				],
				'size' => [
					'default' => 'medium',
					'validate' => fn( $value ) => $value,
				],
			],
		] );

		$parser = new ShortcodeAttsParser( $config );

		$atts = $parser->parse_atts( [], 'test' );
		$this->assertEquals( 'blue', $atts['color'] );
		$this->assertEquals( 'medium', $atts['size'] );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\ShortcodeAttsParser::parse_atts
	 */
	public function test_parse_atts_without_atts_config() {
		$config = new Config( [] );
		$parser = new ShortcodeAttsParser( $config );

		$atts = $parser->parse_atts( [ 'custom' => 'value' ], 'test' );
		$this->assertIsArray( $atts );
	}
}
