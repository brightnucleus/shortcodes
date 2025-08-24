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

/**
 * Shortcode UI Class.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class ShortcodeUI implements ShortcodeUIInterface {

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
	 * Dependencies to be enqueued.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManager
	 */
	protected $dependencies;

	/**
	 * Instantiate Basic Shortcode UI.
	 *
	 * @since 1.0.
	 *
	 * @param string                 $shortcode_tag Tag of the Shortcode.
	 * @param ConfigInterface        $config        Configuration settings.
	 * @param DependencyManager|null $dependencies  Optional. Dependencies that
	 *                                              need to be enqueued.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		$shortcode_tag,
		ConfigInterface $config,
		?DependencyManager $dependencies = null
	) {
		$this->processConfig( $config );

		$this->shortcode_tag = $shortcode_tag;
		$this->dependencies  = $dependencies;
	}

	/**
	 * Register the shortcode UI handler function with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $context Data about the context in which the call is made.
	 * @return void
	 */
	public function register( $context = null ) {
		if ( ! $this->is_needed() ) {
			return;
		}

		\shortcode_ui_register_for_shortcode(
			$this->shortcode_tag,
			$this->config
		);
	}
}
