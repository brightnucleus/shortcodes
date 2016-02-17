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
use BrightNucleus\Dependency\DependencyManagerInterface;

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
class ShortcodeManager {

	use ConfigTrait;

	/*
	 * The delimiter that is used to express key-subkey relations in the config.
	 */
	const CONFIG_SEPARATOR = '/';

	/*
	 * Default classes that are used when omitted from the config.
	 */
	const DEFAULT_SHORTCODE             = __NAMESPACE__ . '\Shortcode';
	const DEFAULT_SHORTCODE_ATTS_PARSER = __NAMESPACE__ . '\ShortcodeAttsParser';
	const DEFAULT_SHORTCODE_UI          = __NAMESPACE__ . '\ShortcodeUI';

	/*
	 * The names of the configuration keys.
	 */
	const KEY_CUSTOM_ATTS_PARSER = 'custom_atts_parser';
	const KEY_CUSTOM_CLASS       = 'custom_class';
	const KEY_CUSTOM_UI          = 'custom_ui';
	const KEY_TAGS               = 'tags';
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
	 * Collection of DependencyManagerInterface objects.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManagerInterface[]
	 */
	protected $dependencies = [ ];

	/**
	 * Collection of ShortcodeUIInterface objects.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeUIInterface[]
	 */
	protected $shortcode_uis = [ ];

	public function __construct(
		ConfigInterface $config,
		$config_key = null,
		DependencyManagerInterface $dependencies
	) {
		$this->processConfig( $config, $config_key );

		if ( ! $this->hasConfigKey( self::KEY_TAGS ) ) {
			return;
		}

		$this->dependencies = $dependencies;

		$this->init_shortcodes();
	}

	/**
	 * Initialize the Shortcode Manager.
	 *
	 * @since 0.1.0
	 */
	public function init_shortcodes() {

		foreach ( $this->getConfigKey( self::KEY_TAGS ) as $tag => $config ) {
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
		$config_key            = $this->get_config_key( $tag );
		$shortcode_class       = $this->get_shortcode_class( $tag );
		$shortcode_atts_parser = $this->get_shortcode_atts_parser_class( $tag );

		$this->shortcodes[] = new $shortcode_class(
			$tag,
			new $shortcode_atts_parser( $tag, $this->config, $config_key ),
			$this->dependencies,
			$this->config,
			$config_key
		);

		if ( $this->hasConfigKey( self::KEY_TAGS, $tag, self::KEY_UI ) ) {
			$this->init_shortcode_ui( $tag );
		}
	}

	/**
	 * Get the configuration key for a specific shortcode tag.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag The shortcode tag to get the config key for.
	 * @return string Configuration key.
	 */
	protected function get_config_key( $tag ) {
		return self::KEY_TAGS . self::CONFIG_SEPARATOR . $tag;
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
		$key             = [ self::KEY_TAGS, $tag, self::KEY_CUSTOM_CLASS ];
		$shortcode_class = $this->hasConfigKey( $key )
			? $this->getConfigKey( $key )
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
		$key         = [ self::KEY_TAGS, $tag, self::KEY_CUSTOM_ATTS_PARSER ];
		$atts_parser = $this->hasConfigKey( $key )
			? $this->getConfigKey( $key )
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
			$this->dependencies,
			$this->config,
			$this->get_config_key( $tag )
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
		$key      = [ self::KEY_TAGS, $tag, self::KEY_CUSTOM_UI ];
		$ui_class = $this->hasConfigKey( $key )
			? $this->getConfigKey( $key )
			: self::DEFAULT_SHORTCODE_UI;
		return $ui_class;
	}

	/**
	 * Register all of the shortcode handlers.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
		array_walk( $this->shortcodes, function ( $shortcode ) {
			/** @var ShortcodeInterface $shortcode */
			$shortcode->register();
		} );

		// This hook only gets fired when Shortcode UI plugin is active.
		\add_action(
			'register_shortcode_ui',
			[ $this, 'register_shortcode_ui', ]
		);
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
}
