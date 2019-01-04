<?php declare( strict_types=1 );

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Shortcode\ShortcodeManager;
use BrightNucleus\View\Location\FilesystemLocation;
use BrightNucleus\View\ViewBuilder;

final class ShortcodesTest extends WP_UnitTestCase {

	public function test_it_can_register_shortcodes_with_wordpress() {
		$config = ConfigFactory::createFromArray(
			[
				'some_tag' => [
					'view' => 'some_dynamic_view',
				],
			]
		);

		$viewBuilder = ( new ViewBuilder() )
			->addLocation( new FilesystemLocation( __DIR__ . '/../fixtures' ) );

		( new ShortcodeManager( $config, null, $viewBuilder ) )->register();

		$output = do_shortcode( '[some_tag]' );

		$this->assertStringStartsWith(
			'<p>This is a dynamic view with <code>PHP code</code></p>',
			$output
		);
	}
}
