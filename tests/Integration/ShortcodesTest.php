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

	public function test_shortcode_with_attributes() {
		$config = ConfigFactory::createFromArray(
			[
				'test_attrs' => [
					'view' => 'some_dynamic_view',
					'atts' => [
						'color' => [
							'default' => 'blue',
							'validate' => fn( $value ) => in_array( $value, [ 'red', 'blue', 'green' ], true ) ? $value : 'blue',
						],
						'size' => [
							'default' => 'medium',
							'validate' => fn( $value ) => in_array( $value, [ 'small', 'medium', 'large' ], true ) ? $value : 'medium',
						],
					],
				],
			]
		);

		$viewBuilder = ( new ViewBuilder() )
			->addLocation( new FilesystemLocation( __DIR__ . '/../fixtures' ) );

		( new ShortcodeManager( $config, null, $viewBuilder ) )->register();

		$output = do_shortcode( '[test_attrs color="red" size="large"]' );
		$this->assertStringStartsWith( '<p>This is a dynamic view', $output );

		$output = do_shortcode( '[test_attrs color="invalid"]' );
		$this->assertStringStartsWith( '<p>This is a dynamic view', $output );
	}

	public function test_shortcode_with_content() {
		$config = ConfigFactory::createFromArray(
			[
				'content_tag' => [
					'view' => 'some_dynamic_view',
				],
			]
		);

		$viewBuilder = ( new ViewBuilder() )
			->addLocation( new FilesystemLocation( __DIR__ . '/../fixtures' ) );

		( new ShortcodeManager( $config, null, $viewBuilder ) )->register();

		$output = do_shortcode( '[content_tag]This is inner content[/content_tag]' );
		$this->assertStringStartsWith( '<p>This is a dynamic view', $output );
	}

	public function test_shortcode_manager_with_multiple_shortcodes() {
		$config = ConfigFactory::createFromArray(
			[
				'first_tag' => [
					'view' => 'some_dynamic_view',
				],
				'second_tag' => [
					'view' => 'some_dynamic_view',
				],
			]
		);

		$viewBuilder = ( new ViewBuilder() )
			->addLocation( new FilesystemLocation( __DIR__ . '/../fixtures' ) );

		( new ShortcodeManager( $config, null, $viewBuilder ) )->register();

		$this->assertTrue( shortcode_exists( 'first_tag' ) );
		$this->assertTrue( shortcode_exists( 'second_tag' ) );

		$output1 = do_shortcode( '[first_tag]' );
		$output2 = do_shortcode( '[second_tag]' );

		$this->assertStringStartsWith( '<p>This is a dynamic view', $output1 );
		$this->assertStringStartsWith( '<p>This is a dynamic view', $output2 );
	}

	public function test_shortcode_with_is_needed_condition() {
		$config = ConfigFactory::createFromArray(
			[
				'conditional_tag' => [
					'view' => 'some_dynamic_view',
					'is_needed' => false,
				],
			]
		);

		$viewBuilder = ( new ViewBuilder() )
			->addLocation( new FilesystemLocation( __DIR__ . '/../fixtures' ) );

		( new ShortcodeManager( $config, null, $viewBuilder ) )->register();

		$this->assertFalse( shortcode_exists( 'conditional_tag' ) );
	}
}
