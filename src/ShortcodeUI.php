<?php
/**
 * ShortcodeUI Class.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use Assert;
use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Dependency\DependencyManagerInterface;
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

	/**
	 * Name of the shortcode handler.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $shortcode_tag;

	/**
	 * Dependencies to be enqueued.
	 *
	 * @since 0.1.0
	 *
	 * @var DependencyManagerInterface
	 */
	protected $dependencies;

	/**
	 * Instantiate Basic Shortcode UI.
	 *
	 * @since 1.0.
	 *
	 * @param string                     $shortcode_tag Tag of the Shortcode.
	 * @param ConfigInterface            $config        Configuration settings.
	 * @param DependencyManagerInterface $dependencies  Dependencies that need
	 *                                                  to be enqueued.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		$shortcode_tag,
		ConfigInterface $config,
		DependencyManagerInterface $dependencies
	) {
		Assert\that( $shortcode_tag )->string()->notEmpty();

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

	/**
	 * Register the shortcode UI handler function with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $context Data about the context in which the call is made.
	 * @return boolean Whether the shortcode is needed or not.
	 */
	protected function is_needed( $context = null ) {

		$is_needed = $this->hasConfigKey( 'is_needed' )
			? $this->getConfigKey( 'is_needed' )
			: false;

		// Return true if a callable 'is_needed' evaluates to true.
		if ( is_callable( $is_needed ) && $is_needed( $context ) ) {
			return true;
		}

		return (bool) $is_needed;
	}
}
