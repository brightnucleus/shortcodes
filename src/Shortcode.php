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

use BrightNucleus\Config\ConfigInterface as Config;
use BrightNucleus\Shortcode\ShortcodeAttsParserInterface as ShortcodeAttsParser;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Dependency\DependencyManagerInterface as DependencyManager;
use BrightNucleus\Exception\DomainException;
use BrightNucleus\Exception\RuntimeException;
use BrightNucleus\View\ViewBuilder;
use BrightNucleus\Views;

/**
 * Base Implementation of the Shortcode Interface.
 *
 * This is a basic implementation of the Shortcode Interface that registers one
 * view and passes all attributes unfiltered to that view.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class Shortcode implements ShortcodeInterface {

	use ConfigTrait;
	use CheckNeedTrait;

	/**
	 * Name of the shortcode handler.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $shortcode_tag;

	/**
	 * Parser to parse and validate the shortcode's attributes.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeAttsParserInterface
	 */
	protected $atts_parser;

	/**
	 * Dependencies of the shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManager
	 */
	protected $dependencies;

	/**
	 * View builder instance to use for creating views to render.
	 *
	 * @since 0.4.0
	 *
	 * @var ViewBuilder
	 */
	protected $view_builder;

	/**
	 * Cache context information so we can pass it on to the render() method.
	 *
	 * @var
	 *
	 * @since 0.2.3
	 */
	protected $context;

	/**
	 * Instantiate Basic Shortcode.
	 *
	 * @since 0.1.0
	 * @since 0.4.0 Added optional $view_builder argument.
	 *
	 * @param string                 $shortcode_tag Tag that identifies the
	 *                                              shortcode.
	 * @param Config                 $config        Configuration settings.
	 * @param ShortcodeAttsParser    $atts_parser   Attributes parser and
	 *                                              validator.
	 * @param DependencyManager|null $dependencies  Optional. Dependencies of
	 *                                              the shortcode.
	 * @param ViewBuilder|null       $view_builder  Optional. View builder
	 *                                              instance to use.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		$shortcode_tag,
		Config $config,
		ShortcodeAttsParser $atts_parser,
		DependencyManager $dependencies = null,
		ViewBuilder $view_builder = null
	) {

		$this->processConfig( $config );

		$this->shortcode_tag = $shortcode_tag;
		$this->atts_parser   = $atts_parser;
		$this->dependencies  = $dependencies;
		$this->view_builder  = $view_builder ?? Views::getViewBuilder();
	}

	/**
	 * Register the shortcode handler function with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $context Optional. Arguments to pass on to the Registrable.
	 * @return void
	 */
	public function register( $context = null ) {
		if ( ! $this->is_needed( $context ) ) {
			return;
		}
		$this->context = $context;

		\add_shortcode( $this->get_tag(), [ $this, 'render' ] );
	}

	/**
	 * Get the shortcode tag.
	 *
	 * @since 0.1.0
	 *
	 * @return string Shortcode tag.
	 */
	public function get_tag() {
		return (string) $this->shortcode_tag;
	}

	/**
	 * Render the shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @throws DomainException
	 *
	 * @param array       $atts    Attributes to modify the standard behavior
	 *                             of the shortcode.
	 * @param string|null $content Optional. Content between enclosing
	 *                             shortcodes.
	 * @param string|null $tag     Optional. The tag of the shortcode to
	 *                             render. Ignored by current code.
	 * @return string              The shortcode's HTML output.
	 */
	public function render( $atts, $content = null, $tag = null ) {
		$context = $this->context;
		$atts    = $this->atts_parser->parse_atts( $atts, $this->get_tag() );
		$this->enqueue_dependencies( $this->get_dependency_handles(), $atts );

		return $this->render_view(
			$this->get_view(),
			$context,
			$atts,
			$content
		);
	}

	/**
	 * Enqueue the dependencies that the shortcode needs.
	 *
	 * @since 0.2.9
	 *
	 * @param array $handles Array of dependency handles to enqueue.
	 * @param mixed $context Optional. Context in which to enqueue.
	 */
	protected function enqueue_dependencies( $handles, $context = null ) {
		if ( ! $this->dependencies || count( $handles ) < 1 ) {
			return;
		}

		foreach ( $handles as $handle ) {
			$found = $this->dependencies->enqueue_handle(
				$handle,
				$context,
				true
			);
			if ( ! $found ) {
				$message = sprintf(
					__( 'Could not enqueue dependency "%1$s" for shortcode "%2$s".',
						'bn-shortcodes' ),
					$handle,
					$this->get_tag()
				);
				trigger_error( $message, E_USER_WARNING );
			}
		}

	}

	/**
	 * Get an array of dependency handles for the current shortcode.
	 *
	 * @since 0.2.7
	 *
	 * @return array Array of strings that are registered dependency handles.
	 */
	protected function get_dependency_handles() {
		if ( ! $this->hasConfigKey( 'dependencies' ) ) {
			return [ ];
		}
		return (array) $this->getConfigKey( 'dependencies' );
	}

	/**
	 * Get the rendered HTML for a given view.
	 *
	 * @since 0.2.6
	 *
	 * @param string      $uri     URI of the view to render.
	 * @param mixed       $context The context to pass through to the view.
	 * @param array       $atts    The shortcode attribute values to pass
	 *                             through to the view.
	 * @param string|null $content Optional. The inner content of the shortcode.
	 * @return string HTML rendering of the view.
	 */
	protected function render_view( $uri, $context, $atts, $content = null ) {
		return $this->view_builder
			->create( $uri )
			->render(
				array_merge(
					(array) $context,
					(array) $atts,
					(array) $content )
			);
	}

	/**
	 * Get the name of the view to render.
	 *
	 * @since 0.2.6
	 *
	 * @return string Name of the view to render.
	 */
	protected function get_view() {
		if ( ! $this->hasConfigKey( 'view' ) ) {
			return '';
		}
		$view = $this->getConfigKey( 'view' );

		return $view;
	}

	/**
	 * Execute this shortcode directly from code.
	 *
	 * @since 0.2.4
	 *
	 * @param array       $atts    Array of attributes to pass to the shortcode.
	 * @param string|null $content Inner content to pass to the shortcode.
	 * @return string|false Rendered HTML.
	 */
	public function do_this( array $atts = [ ], $content = null ) {
		\BrightNucleus\Shortcode\do_tag( $this->get_tag(), $atts, $content );
	}
}
