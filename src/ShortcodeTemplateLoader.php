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

use Gamajo_Template_Loader;

/**
 * Class ShortcodeTemplateLoader.
 *
 * @since   0.2.6
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class ShortcodeTemplateLoader extends Gamajo_Template_Loader {

	/**
	 * Directory name of the shortcode views.
	 *
	 * @var string
	 *
	 * @since 0.2.6
	 */
	protected $view_directory;

	/**
	 * Instantiate a ShortcodeTemplateLoader object.
	 *
	 * @since 0.2.6
	 *
	 * @param string $filter_prefix Prefix for filter names.
	 * @param string $template_dir  Directory name for custom templates.
	 * @param string $view_dir      Directory name for the shortcode views.
	 */
	public function __construct( $filter_prefix, $template_dir, $view_dir ) {
		$this->filter_prefix            = $filter_prefix;
		$this->theme_template_directory = $template_dir;
		$this->view_directory           = $view_dir;
	}

	/**
	 * Return the path to the templates directory in this plugin.
	 *
	 * @since 0.2.6
	 *
	 * @return string
	 */
	protected function get_templates_dir() {
		return $this->view_directory;
	}
}
