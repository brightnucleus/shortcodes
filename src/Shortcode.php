<?php
/**
 * Shortcode Base Implementation.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use Assert;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Dependency\DependencyManagerInterface as DependencyManager;
use BrightNucleus\Exception\DomainException;
use BrightNucleus\Exception\RuntimeException;

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
	 *
	 * @param string                 $shortcode_tag Tag that identifies the
	 *                                              shortcode.
	 * @param ConfigInterface        $config        Configuration settings.
	 * @param ShortcodeAttsParser    $atts_parser   Attributes parser and
	 *                                              validator.
	 * @param DependencyManager|null $dependencies  Optional. Dependencies of
	 *                                              the shortcode.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		$shortcode_tag,
		ConfigInterface $config,
		ShortcodeAttsParser $atts_parser,
		DependencyManager $dependencies = null
	) {

		Assert\that( $shortcode_tag )->string()->notEmpty();

		$this->processConfig( $config );

		$this->shortcode_tag = $shortcode_tag;
		$this->atts_parser   = $atts_parser;
		$this->dependencies  = $dependencies;
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
	 * Check whether the shortcode is needed.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $context Data about the context in which the call is made.
	 * @return boolean Whether the shortcode is needed or not.
	 */
	protected function is_needed( $context = null ) {

		$is_needed = $this->hasConfigKey( 'is_needed' )
			? $this->getConfigKey( 'is_needed' )
			: false;

		if ( is_callable( $is_needed ) ) {
			return $is_needed( $context );
		}

		return (bool) $is_needed;
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
	 * @param  array       $atts    Attributes to modify the standard behavior
	 *                              of the shortcode.
	 * @param  string|null $content Optional. Content between enclosing
	 *                              shortcodes.
	 * @param string|null  $tag     Optional. The tag of the shortcode to
	 *                              render.
	 * @return string               The shortcode's HTML output.
	 */
	public function render( $atts, $content = null, $tag = null ) {
		$context = $this->context;
		$atts    = $this->atts_parser->parse_atts( $atts, $this->get_tag() );

		if ( $this->dependencies ) {
			$this->dependencies->enqueue( $atts );
		}

		if ( ! $this->hasConfigKey( 'view' ) ) {
			return '';
		}
		$view = $this->getConfigKey( 'view' );

		Assert\that( $view )->string()->notEmpty()->file();

		ob_start();
		include( $this->config['view'] );

		return ob_get_clean();
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
