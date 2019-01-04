<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

final class TemplatedShortcodeTest extends TestCase {

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
}
