<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

final class ShortcodeUITest extends TestCase {

	public function test_it_can_be_instantiated() {
		$config      = $this->createMock( ConfigInterface::class );

		$shortcodeUI = new ShortcodeUI( 'some_tag', $config );

		$this->assertInstanceOf( ShortcodeUI::class, $shortcodeUI );
		$this->assertInstanceOf( ShortcodeUIInterface::class, $shortcodeUI );
	}
}
