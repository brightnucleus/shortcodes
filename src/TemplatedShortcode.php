<?php
/**
 * Templated Shortcode Implementation.
 *
 * This version of the shortcode uses Gamajo/TemplateLoader to let you override
 * the shortcode views from your theme.
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
use BrightNucleus\Dependency\DependencyManagerInterface as DependencyManager;
use BrightNucleus\Exception\RuntimeException;
use Gamajo_Template_Loader;

/**
 * Templated Implementation of the Shortcode Interface.
 *
 * This version of the Shortcode
 *
 * @since   0.2.6
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class TemplatedShortcode extends Shortcode {

	/**
	 * Template loader that allows a theme to override the shortcode's views.
	 *
	 * @var Gamajo_Template_Loader|null
	 */
	protected $template_loader;

	/**
	 * Instantiate Basic Shortcode.
	 *
	 * @since 0.2.6
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

		parent::__construct(
			$shortcode_tag,
			$config,
			$atts_parser,
			$dependencies
		);

		$this->template_loader = $this->init_template_loader();
	}

	/**
	 * Initialize the template loader class.
	 *
	 * @since 0.2.6
	 *
	 * @return Gamajo_Template_Loader
	 */
	public function init_template_loader() {
		$loader_class  = $this->hasConfigKey( 'template', 'custom_loader' )
			? $this->getConfigKey( 'template', 'custom_loader' )
			: $this->get_default_template_loader_class();
		$filter_prefix = $this->hasConfigKey( 'template', 'filter_prefix' )
			? $this->getConfigKey( 'template', 'filter_prefix' )
			: $this->get_default_filter_prefix();
		$template_dir  = $this->hasConfigKey( 'template', 'template_directory' )
			? $this->getConfigKey( 'template', 'template_directory' )
			: $this->get_default_template_directory();
		$view_dir      = $this->hasConfigKey( 'view' )
			? $this->get_directory_from_view( $this->getConfigKey( 'view' ) )
			: $this->get_default_view_directory();

		return new $loader_class(
			$filter_prefix,
			$template_dir,
			$view_dir
		);
	}

	/**
	 * Get the default template loader class that is used when none is defined
	 * in the config file.
	 *
	 * @since 0.2.6
	 *
	 * @return string The default template laoder class to use.
	 */
	protected function get_default_template_loader_class() {
		return 'BrightNucleus\Shortcode\ShortcodeTemplateLoader';
	}

	/**
	 * Get the default filter prefix that is used when none is defined in the
	 * config file.
	 *
	 * Defaults to 'bn_shortcode.'.
	 *
	 * @since 0.2.6
	 *
	 * @return string Default filter prefix to use.
	 */
	protected function get_default_filter_prefix() {
		return 'bn_shortcode';
	}

	/**
	 * Get the default template directory that is used when none is defined in
	 * the config file.
	 *
	 * Defaults to 'bn_shortcode'.
	 *
	 * @since 0.2.6
	 *
	 * @return string Default template directory to use.
	 */
	protected function get_default_template_directory() {
		return 'bn_shortcode';
	}

	/**
	 * Get the directory for a given view file.
	 *
	 * @since 0.2.6
	 *
	 * @param string $view View file to extract the directory from.
	 * @return string Directory that contains the given view.
	 */
	protected function get_directory_from_view( $view ) {
		return dirname( $view );
	}

	/**
	 * Get the default view directory that is used when none is defined in the
	 * config file.
	 *
	 * Defaults to 'views/shortcodes'. Will probably need to be changed into an
	 * absolute path if the shortcodes package is pulled in through Composer.
	 *
	 * @since 0.2.6
	 *
	 * @return string Default view directory to use.
	 */
	protected function get_default_view_directory() {
		return 'views/shortcodes';
	}

	/**
	 * Get the rendered HTML for a given view.
	 *
	 * @since 0.2.6
	 *
	 * @param string $view    The view to render.
	 * @param mixed  $context The context to pass through to the view.
	 * @return string HTML rendering of the view.
	 */
	protected function render_view( $view, $context ) {
		if ( empty( $view ) ) {
			return '';
		}

		$view = $this->get_view_slug( $view );

		$this->template_loader->set_template_data( (array) $context, 'context' );

		ob_start();
		$this->template_loader->get_template_part( $view );
		return ob_get_clean();
	}

	/**
	 * Get the slug for a given view.
	 *
	 * @since 0.2.6
	 *
	 * @param string $view The view to get the slug for.
	 * @return string Slug that can be passed into `get_template_part()`.
	 */
	protected function get_view_slug( $view ) {
		return $this->maybe_strip_extension( basename( $view ) );
	}

	/**
	 * Strip the extension for a given view filename if it includes an
	 * extension.
	 *
	 * @since 0.2.6
	 *
	 * @param string $view The view that maybe needs its extension stripped.
	 * @return string Extension-less view.
	 */
	protected function maybe_strip_extension( $view ) {
		return pathinfo( $view, PATHINFO_FILENAME );
	}
}
