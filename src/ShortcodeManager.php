<?php
/**
 * Shortcode Manager.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Dependency\DependencyManagerInterface as DependencyManager;
use BrightNucleus\Exception\RuntimeException;

/**
 * Shortcode Manager.
 *
 * This class manages all the shortcodes that it gets passed within a
 * ConfigInterface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class ShortcodeManager implements ShortcodeManagerInterface {

	use ConfigTrait;

	/*
	 * The delimiter that is used to express key-subkey relations in the config.
	 */
	const CONFIG_SEPARATOR = '/';

	/*
	 * Default classes that are used when omitted from the config.
	 */
	const DEFAULT_SHORTCODE             = 'BrightNucleus\Shortcode\Shortcode';
	const DEFAULT_SHORTCODE_ATTS_PARSER = 'BrightNucleus\Shortcode\ShortcodeAttsParser';
	const DEFAULT_SHORTCODE_UI          = 'BrightNucleus\Shortcode\ShortcodeUI';

	/*
	 * The names of the configuration keys.
	 */
	const KEY_CUSTOM_ATTS_PARSER = 'custom_atts_parser';
	const KEY_CUSTOM_CLASS       = 'custom_class';
	const KEY_CUSTOM_UI          = 'custom_ui';
	const KEY_UI                 = 'ui';
	/**
	 * Collection of ShortcodeInterface objects.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeInterface[]
	 */
	protected $shortcodes = [ ];

	/**
	 * DependencyManagerInterface implementation.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManagerInterface
	 */
	protected $dependencies;

	/**
	 * Collection of ShortcodeUIInterface objects.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeUIInterface[]
	 */
	protected $shortcode_uis = [ ];

	/**
	 * Instantiate a ShortcodeManager object.
	 *
	 * @since 0.1.0
	 *
	 * @param ConfigInterface        $config       Configuration to set up the
	 *                                             shortcodes.
	 * @param DependencyManager|null $dependencies Optional. Dependencies that
	 *                                             are needed by the shortcodes.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		ConfigInterface $config,
		DependencyManager $dependencies = null
	) {
		$this->processConfig( $config );
		$this->dependencies = $dependencies;

		$this->init_shortcodes();
	}

	/**
	 * Initialize the Shortcode Manager.
	 *
	 * @since 0.1.0
	 */
	protected function init_shortcodes() {
		foreach ( $this->getConfigKeys() as $tag ) {
			$this->init_shortcode( $tag );
		}
	}

	/**
	 * Initialize a single shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag The tag of the shortcode to register.
	 */
	protected function init_shortcode( $tag ) {
		$shortcode_class       = $this->get_shortcode_class( $tag );
		$shortcode_atts_parser = $this->get_shortcode_atts_parser_class( $tag );

		$atts_parser        = new $shortcode_atts_parser(
			$this->config->getSubConfig( $tag )
		);
		$this->shortcodes[] = new $shortcode_class(
			$tag,
			$this->config->getSubConfig( $tag ),
			$atts_parser,
			$this->dependencies
		);

		if ( $this->hasConfigKey( $tag, self::KEY_UI ) ) {
			$this->init_shortcode_ui( $tag );
		}
	}

	/**
	 * Get the class name of an implementation of the ShortcodeInterface.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag Shortcode tag to get the class for.
	 * @return string Class name of the Shortcode.
	 */
	protected function get_shortcode_class( $tag ) {
		$shortcode_class = $this->hasConfigKey( $tag, self::KEY_CUSTOM_CLASS )
			? $this->getConfigKey( $tag, self::KEY_CUSTOM_CLASS )
			: self::DEFAULT_SHORTCODE;
		return $shortcode_class;
	}

	/**
	 * Get the class name of an implementation of the
	 * ShortcodeAttsParsersInterface.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag Shortcode tag to get the class for.
	 * @return string Class name of the ShortcodeAttsParser.
	 */
	protected function get_shortcode_atts_parser_class( $tag ) {
		$atts_parser = $this->hasConfigKey( $tag, self::KEY_CUSTOM_ATTS_PARSER )
			? $this->getConfigKey( $tag, self::KEY_CUSTOM_ATTS_PARSER )
			: self::DEFAULT_SHORTCODE_ATTS_PARSER;
		return $atts_parser;
	}

	/**
	 * Initialize the Shortcode UI for a single shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag The tag of the shortcode to register the UI for.
	 */
	protected function init_shortcode_ui( $tag ) {
		$shortcode_ui_class = $this->get_shortcode_ui_class( $tag );

		$this->shortcode_uis[] = new $shortcode_ui_class(
			$tag,
			$this->config->getSubConfig( $tag, self::KEY_UI ),
			$this->dependencies
		);
	}

	/**
	 * Get the class name of an implementation of the ShortcodeUIInterface.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag Configuration settings.
	 * @return string Class name of the ShortcodeUI.
	 */
	protected function get_shortcode_ui_class( $tag ) {
		$ui_class = $this->hasConfigKey( $tag, self::KEY_CUSTOM_UI )
			? $this->getConfigKey( $tag, self::KEY_CUSTOM_UI )
			: self::DEFAULT_SHORTCODE_UI;
		return $ui_class;
	}

	/**
	 * Register all of the shortcode handlers.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $context Optional. Context information to pass to shortcode.
	 * @return void
	 */
	public function register( $context = null ) {
		$context                  = $this->validate_context( $context );
		$context['page_template'] = $this->get_page_template();

		array_walk( $this->shortcodes,
			function ( $shortcode ) use ( $context ) {
				/** @var ShortcodeInterface $shortcode */
				$shortcode->register( $context );
			} );

		// This hook only gets fired when Shortcode UI plugin is active.
		\add_action(
			'register_shortcode_ui',
			[ $this, 'register_shortcode_ui', ]
		);
	}

	/**
	 * Validate the context to make sure it is an array.
	 *
	 * @since 0.2.3
	 *
	 * @param mixed $context The context as passed in by WordPress.
	 * @return array Validated context.
	 */
	protected function validate_context( $context ) {
		if ( is_string( $context ) ) {
			return [ 'wp_context' => $context ];
		}
		return (array) $context;
	}

	/**
	 * Get the name of the page template.
	 *
	 * @since 0.1.0
	 *
	 * @return string Name of the page template.
	 */
	protected function get_page_template() {
		$template = str_replace(
			\trailingslashit( \get_stylesheet_directory() ),
			'',
			\get_page_template()
		);
		return $template;
	}

	/**
	 * Register the shortcode UI handlers.
	 *
	 * @since 0.1.0
	 */
	public function register_shortcode_ui() {
		$template = $this->get_page_template();
		$context  = [ 'page_template' => $template ];

		array_walk( $this->shortcode_uis,
			function ( $shortcode_ui ) use ( $context ) {
				/** @var ShortcodeUIInterface $shortcode_ui */
				$shortcode_ui->register( $context );
			}
		);
	}
}
