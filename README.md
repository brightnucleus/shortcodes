# Bright Nucleus Shortcodes Component

### Config-driven WordPress shortcodes.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/badges/build.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/shortcodes/build-status/master)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/63e9bcca67d6488a8f9f0721e4c83ee3)](https://www.codacy.com/app/BrightNucleus/shortcodes)
[![Code Climate](https://codeclimate.com/github/brightnucleus/shortcodes/badges/gpa.svg)](https://codeclimate.com/github/brightnucleus/shortcodes)

[![Latest Stable Version](https://poser.pugx.org/brightnucleus/shortcodes/v/stable)](https://packagist.org/packages/brightnucleus/shortcodes)
[![Total Downloads](https://poser.pugx.org/brightnucleus/shortcodes/downloads)](https://packagist.org/packages/brightnucleus/shortcodes)
[![Latest Unstable Version](https://poser.pugx.org/brightnucleus/shortcodes/v/unstable)](https://packagist.org/packages/brightnucleus/shortcodes)
[![License](https://poser.pugx.org/brightnucleus/shortcodes/license)](https://packagist.org/packages/brightnucleus/shortcodes)

This is a WordPress shortcodes component that lets you define shortcodes through a config file, complete with dependencies, localization and Shortcake UI.

## Table Of Contents

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Configuration Schema](#configuration-schema)
* [Registering A Basic Shortcode](#registering-a-basic-shortcode)
    * [Configuration File](#configuration-file)
    * [Template File](#template-file)
    * [Initialization](#initialization)
* [Using Custom Classes](#using-custom-classes)
* [Using Relative Views](#using-relative-views)
* [Contributing](#contributing)
* [License](#license)

## Installation

The best way to use this component is through Composer:

```BASH
composer require brightnucleus/shortcodes
```


## Basic Usage

To use this component, you'll need to:

1. instantiate the `ShortcodeManager` class;
2. inject an object implementing the `BrightNucleus\Config\ConfigInterface` through its constructor;
3. call its `register()` method.

```PHP
use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Shortcode\ShortcodeManager;

$config = ConfigFactory::create( __DIR__ . '/../config/example_config.php');
$shortcode_manager = new ShortcodeManager(
	$config->getSubConfig( 'ShortcodeManager' )
);
$shortcode_manager->register();
```

## Configuration Schema

```PHP
$shortcodes_config = [

	/* For each shortcode you wish to define, you'll need one separate entry at
	 * the root config entry passed in to ShortcodeManager. The name of that
	 * entry is used as the shortcode tag.
	 */
	'shortcode_tag' => [

		/* Path to a template that is used to render the shortcode.
		 * The path is relative to the configuration file.
		 */
		'view' => __DIR__ . '/../views/shortcodes/view_file.php',

		/* Customised ShortcodeInterface implementation. (optional)
		 * You can use this to completely customize the standard shortcode
		 * class behavior.
		 * Omit to use default `Shortcode` class.
		 * This can be either a fully qualified class name or a callable.
		 */
		'custom_class' => '\BrightNucleus\Shortcodes\Shortcode',

		/* Customised ShortcodeAttsParserInterface implementation. (optional)
		 * You can use this to completely customize the way shortcode attributes
		 * are parsed.
		 * Omit to use default `ShortcodeAttsParser` class.
		 * This can be either a fully qualified class name or a callable.
		 */
		'custom_atts_parser' => '\BrightNucleus\Shortcodes\ShortcodeAttsParser',

		/* Collection of attributes that can be used with the shortcode.
		 * These attributes will be processed by the
		 * `ShortcodeAttsParserInterface` implementation that is being used.
		 */
		'atts' => [

			/* Shortcode attribute name.
			 * These are the optional attributes that you can append to your
			 * shortcode within the WP editor: [shortcode att_name='value'].
			 * NB: lower-cased by WP, so no camelCase or UPPER_CASE.
			 */
			'attribute_name'    => [

				/* Provided that you use the default `ShortcodeAttsParser`
				 * implementation, you can define a `default` value for each
				 * attribute, as well as an optional `validate` callable that
				 * gets evaluated to a boolean.
				 */
				'default'  => 'default_value',
				'validate' => function ( $att ) {
					return some_validation_function( $att );
				},
			],
		],

		/* Customised ShortcodeUIInterface implementation. (optional)
		 * You can use this to completely customize the standard shortcode
		 * user interface class behavior.
		 * Omit to use default `ShortcodeUI` class.
		 * This can be either a fully qualified class name or a callable.
		 */
		'custom_ui' => '\BrightNucleus\Shortcodes\ShortcodeUI',

		/* Besides one additional keys that ShortcodeManager recognizes, the
		 * 'ui' subkey gets passed as is to the Shortcake UI plugin.
		 * Refer to the Shortcake documentation for details about the syntax:
		 * https://github.com/wp-shortcake/shortcake/wiki/Registering-Shortcode-UI
		 */
		'ui'   => [

			/* Whether the shortcode UI (along with its dependencies) is needed
			 * within the current context or not. If this is a callable, it gets
			 * executed and its result evaluated to boolean.
			 */
			'is_needed' => function ( $context ) {
				return true;
			},

			// [ Shortcake configuration keys. ]

		],
	]
];
```

## Registering a basic shortcode

For the following example, we'll register a new shortcode that provides a simple `[button]` shortcode. We want the shortcode to be configurable through Shortcake.

### Configuration File

First, we need to define the shortcode through a configuration file.

```PHP
<?php namespace Example\Plugin;

/* ShortcodeManager configuration.
 */
$shortcodes = [
	// Let's define a new button.
	'button'          => [
		'view' => __DIR__ . '/../views/shortcodes/button.php',
		'atts' => [
			// It will accept a caption...
			'caption' => [
				'validate' => function ( $att ) {
					return ( null !== $att )
						? esc_attr( $att )
						: null;
				},
				'default'  => 'Straight to Google!',
			],
			// ...and a URL.
			'url'     => [
				'validate' => function ( $att ) {
					return ( null !== $att )
						? esc_attr( $att )
						: null;
				},
				'default'  => 'https://www.google.com/',
			],
		],
		// We also want a user interface for that button.
		'ui'   => [
			// Let's call it "Example Button".
			'label'         => esc_html__(
				'Example Button',
				'example-plugin'
			),
			'listItemImage' => 'dashicons-search',
			// We only want to make it available when editing a "page".
			'post_type'     => [ 'page' ],
			// It is always needed, so no extra checks to load it conditionally.
			'is_needed'     => function ( $context ) { return true; },
			// We also need to configure the Shortcake input fields.
			'attrs'         => [
				[
					'label'       => esc_html__(
						'Caption',
						'example-plugin'
					),
					'description' => esc_html__(
						'The caption that is shown on the button.',
						'example-plugin'
					),
					'attr'        => 'caption',
					'type'        => 'text',
					'value'       => 'Straight to Google!',
				],
				[
					'label'       => esc_html__(
						'URL',
						'example-plugin'
					),
					'description' => esc_html__(
						'Target URL where the button will lead to when pressed.',
						'example-plugin'
					),
					'attr'        => 'url',
					'type'        => 'url',
					'value'       => 'https://www.google.com/',
				],
			],
		],
	],
];

/* Plugin settings.
 */
$plugin_settings = [
	'ShortcodeManager'  => $shortcodes,
];

/* Return with Vendor/Package prefix.
 */
return [
	'Example' => [
		'Plugin' => $plugin_settings,
	],
];
```

### Template file

Then, we'll need to write a template that can be rendered by the shortcode.

```PHP
<?php namespace Example\Plugin;

/**
 * Button Shortcode Template
 */

// The `$atts` array (as well as the inner `$content` variable) will be
// available from within this template.

?>
<div class="example button class">
	<a class="button radius"
	href="<?php echo esc_url( $atts['url'] ); ?>"><?php echo esc_html( $atts['caption'] ); ?></a>
</div>
```

### Initialization

Finally, we'll need to initialize the `ShortcodeManager` with the configuration file, and let it `register()` its shortcodes.

```PHP
<?php namespace Example\Plugin;

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Shortcode\ShortcodeManager;

const PLUGIN_PREFIX            = 'Example\Plugin';
const SHORTCODE_MANAGER_PREFIX = 'ShortcodeManager';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load Composer autoloader.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Load configuration file.
$config = ConfigFactory::create( __DIR__ . '/config/example.php' );

// Initialize Shortcode Manager.
$shortcode_manager = new ShortcodeManager(
	$config->getSubConfig( PLUGIN_PREFIX, SHORTCODE_MANAGER_PREFIX )
);

// Hook Shortcode Manager up to WordPress action.
\add_action( 'init', [ $shortcode_manager, 'register' ] );
```

## Using Custom Classes

The actual implementations to be used for the following interfaces can be changed through the Config files:

* `BrightNucleus\Shortcode\ShortcodeInterface`
* `BrightNucleus\Shortcode\ShortcodeAttsParserInterface`
* `BrightNucleus\Shortcode\ShortcodeUIInterface`

The Config files accepts a key for overriding each of these. You can pass either a fully qualified class name or a callable that acts as a factory.

When using a callable, the arguments that are passed to that callable are the same as the constructor gets for the default implementation of each of these.

## Using Relative Views

The underlying implementation uses the `brightnucleus/view` package to render the actual views for each shortcode. The default behavior already deals with absolute paths and can render any type of views that the Views engine being used can handle. If no specific instance of a `ViewBuilder` was injected, then the `ShortcodeManager` will rely on the one provided by the `Views` Facade.

To adapt the locations that the view engine looks in for relative view URIs or to configure the available rendering engines, you can either adapt the instance centrally available through the Facade, or, preferably, inject your own custom `ViewBuilder` instance into the `ShortcodeManager` as the third constructor argument.

Once you've added one or more locations in this way, you can use relative URIs in your config file. They do not even need to contain the extension, as long as that can be inferred by the engines that are known to the view builder. This makes overriding the views later on very flexible, in that you can not only override shortcode markup in your theme that was defined in your plugin, but you can also use a different engine than was originally use.

Refer to the [`brightnucleus/view` documentation](https://github.com/brightnucleus/view/blob/master/README.md) on how to go about configuring a `ViewBuilder` instance.

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

Copyright (c) 2016 Alain Schlesser, Bright Nucleus

This code is licensed under the [MIT License](LICENSE).
