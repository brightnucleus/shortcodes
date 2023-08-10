<?php
/**
 * Bright Nucleus Shortcode Component.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Dependency\DependencyManagerInterface as DependencyManager;
use BrightNucleus\Exception\RuntimeException;
use BrightNucleus\Invoker\InstantiatorTrait;
use BrightNucleus\Shortcode\Exception\FailedToInstantiateObject;
use BrightNucleus\View\ViewBuilder;
use BrightNucleus\Views;
use Exception;

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
	use InstantiatorTrait;

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
	protected $shortcodes = [];

	/**
	 * DependencyManagerInterface implementation.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManager
	 */
	protected $dependencies;

	/**
	 * View builder instance to use for rendering views.
	 *
	 * @since 0.4.0
	 *
	 * @var ViewBuilder
	 */
	protected $view_builder;

	/**
	 * Collection of ShortcodeUIInterface objects.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeUIInterface[]
	 */
	protected $shortcode_uis = [];

	/**
	 * External injector to use.
	 *
	 * @var object
	 */
	protected $injector;

	/**
	 * Instantiate a ShortcodeManager object.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Add optional $viewBuilder argument.
	 *
	 * @param ConfigInterface        $config       Configuration to set up the
	 *                                             shortcodes.
	 * @param DependencyManager|null $dependencies Optional. Dependencies that
	 *                                             are needed by the
	 *                                             shortcodes.
	 * @param ViewBuilder|null       $view_builder Optional. View builder
	 *                                             instance to use for
	 *                                             rendering views.
	 *
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		ConfigInterface $config,
		DependencyManager $dependencies = null,
		ViewBuilder $view_builder = null
	) {
		$this->processConfig( $config );
		$this->dependencies = $dependencies;
		$this->view_builder = $view_builder ?? Views::getViewBuilder();
	}

	/**
	 * Use an external injector to instantiate the different classes.
	 *
	 * The injector will
	 * @param object $injector Injector to use.
	 */
	public function with_injector( $injector ) {
		if ( ! method_exists( $injector, 'make' ) ) {
			throw new RuntimeException(
				'Invalid injector provided, it does not have a make() method.'
			);
		}

		$this->injector = $injector;
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
	 *
	 * @throws FailedToInstantiateObject If the Shortcode Atts Parser object
	 *                                   could not be instantiated.
	 * @throws FailedToInstantiateObject If the Shortcode object could not be
	 *                                   instantiated.
	 */
	protected function init_shortcode( $tag ) {
		$shortcode_class       = $this->get_shortcode_class( $tag );
		$shortcode_atts_parser = $this->get_shortcode_atts_parser_class( $tag );

		$atts_parser = $this->instantiate(
			ShortcodeAttsParserInterface::class,
			$shortcode_atts_parser,
			[ 'config' => $this->config->getSubConfig( $tag ) ]
		);

		$this->shortcodes[] = $this->instantiate(
			ShortcodeInterface::class,
			$shortcode_class,
			[
				'shortcode_tag' => $tag,
				'config'        => $this->config->getSubConfig( $tag ),
				'atts_parser'   => $atts_parser,
				'dependencies'  => $this->dependencies,
				'view_builder'  => $this->view_builder,
			]
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
	 *
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
	 *
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
	 * @param string $tag                The tag of the shortcode to register
	 *                                   the UI for.
	 *
	 * @throws FailedToInstantiateObject If the Shortcode UI object could not
	 *                                   be instantiated.
	 */
	protected function init_shortcode_ui( $tag ) {
		$shortcode_ui_class = $this->get_shortcode_ui_class( $tag );

		$this->shortcode_uis[] = $this->instantiate(
			ShortcodeUIInterface::class,
			$shortcode_ui_class,
			[
				'shortcode_tag' => $tag,
				'config'        => $this->config->getSubConfig( $tag, self::KEY_UI ),
				'dependencies'  => $this->dependencies,
			]
		);
	}

	/**
	 * Get the class name of an implementation of the ShortcodeUIInterface.
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag Configuration settings.
	 *
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
	 *
	 * @return void
	 */
	public function register( $context = null ) {
		$this->init_shortcodes();

		$context                  = $this->validate_context( $context );
		$context['page_template'] = $this->get_page_template();

		array_walk( $this->shortcodes,
			function ( ShortcodeInterface $shortcode ) use ( $context ) {
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
	 *
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
			function ( ShortcodeUIInterface $shortcode_ui ) use ( $context ) {
				$shortcode_ui->register( $context );
			}
		);
	}

	/**
	 * Execute a specific shortcode directly from code.
	 *
	 * @since 0.2.4
	 *
	 * @param string $tag     Tag of the shortcode to execute.
	 * @param array  $atts    Array of attributes to pass to the shortcode.
	 * @param null   $content Inner content to pass to the shortcode.
	 *
	 * @return string|false Rendered HTML.
	 */
	public function do_tag( $tag, array $atts = [], $content = null ) {
		return \BrightNucleus\Shortcode\do_tag( $tag, $atts, $content );
	}

	/**
	 * Instantiate a new object through either a class name or a factory method.
	 *
	 * @since 0.3.0
	 *
	 * @param string          $interface Interface the object needs to
	 *                                   implement.
	 * @param callable|string $class     Fully qualified class name or factory
	 *                                   method.
	 * @param array           $args      Arguments passed to the constructor or
	 *                                   factory method.
	 *
	 * @return object Object that implements the interface.
	 * @throws FailedToInstantiateObject If no valid object could be
	 *                                   instantiated.
	 */
	protected function instantiate( $interface, $class, array $args ) {
		try {
			if ( is_callable( $class ) ) {
				$class = call_user_func_array( $class, array_values( $args ) );
			}

			if ( is_string( $class ) ) {
				if ( null !== $this->injector ) {
					$class = $this->injector->make( $class, $args );
				} else {
					$class = $this->instantiateClass( $class, $args );
				}
			}
		} catch ( Exception $exception ) {
			throw FailedToInstantiateObject::fromFactory(
				$class,
				$interface,
				$exception
			);
		}

		if ( ! is_subclass_of( $class, $interface ) ) {
			throw FailedToInstantiateObject::fromInvalidObject(
				$class,
				$interface
			);
		}

		return $class;
	}
}
