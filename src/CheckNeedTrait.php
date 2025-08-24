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

/**
 * Check Need Trait.
 *
 * Provides the `is_needed()` method.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
trait CheckNeedTrait {

	/**
	 * Check whether an element is needed.
	 *
	 * @since 0.2.0
	 *
	 * @param mixed $context Data about the context in which the call is made.
	 * @return boolean Whether the element is needed or not.
	 */
	protected function is_needed( $context = null ) {

		$is_needed = $this->hasConfigKey( 'is_needed' )
			? $this->getConfigKey( 'is_needed' )
			: true;

		if ( is_callable( $is_needed ) ) {
			return $is_needed( $context );
		}

		return (bool) $is_needed;
	}

	/**
	 * Check whether the Config has a specific key.
	 *
	 * This needs to be implemented in a class that wants to use CheckNeedTrait.
	 *
	 * @since 0.2.10
	 *
	 * @param string|array $_ List of keys.
	 * @return bool Whether the key is known.
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid,Squiz.Commenting.FunctionComment.Missing -- Must match ConfigTrait interface
	abstract protected function hasConfigKey( $_ );

	/**
	 * Get the Config value for a specific key.
	 *
	 * This needs to be implemented in a class that wants to use CheckNeedTrait.
	 *
	 * @since 0.2.10
	 *
	 * @param string|array $_ List of keys.
	 * @return mixed Value of the key.
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid,Squiz.Commenting.FunctionComment.Missing -- Must match ConfigTrait interface
	abstract protected function getConfigKey( $_ );
}
