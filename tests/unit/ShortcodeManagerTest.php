<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

final class ShortcodeManagerTest extends TestCase {

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
}
