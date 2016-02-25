<?php
/**
 * Check Need Trait.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
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
}
