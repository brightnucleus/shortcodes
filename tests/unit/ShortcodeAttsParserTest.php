<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

final class ShortcodeAttsParserTest extends TestCase {

	public function test_it_can_be_instantiated() {
		$config     = $this->createMock( ConfigInterface::class );

		$attsParser = new ShortcodeAttsParser( $config );

		$this->assertInstanceOf( ShortcodeAttsParser::class, $attsParser );
		$this->assertInstanceOf(
			ShortcodeAttsParserInterface::class,
			$attsParser
		);
	}
}
