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

namespace BrightNucleus\Shortcode\Exception;

use BrightNucleus\Exception\RuntimeException;
use Exception;

/**
 * Class FailedToInstantiateObject.
 *
 * @since   0.3.0
 *
 * @package BrightNucleus\Shortcode\Exception
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class FailedToInstantiateObject extends RuntimeException implements ShortcodeException {

	/**
	 * Create a new instance from a passed-in class name or factory callable.
	 *
	 * @since 0.3.0
	 *
	 * @param mixed     $factory        Class name or factory callable.
	 * @param string    $interface_name Interface name that the object should have
	 *                                  implemented.
	 * @param Exception $exception      Exception that was caught.
	 * @return static Instance of an exception.
	 */
	public static function fromFactory( $factory, $interface_name, $exception = null ) {
		$reason = $exception instanceof Exception
			? " Reason: {$exception->getMessage()}"
			: '';

		if ( is_callable( $factory ) ) {
			$message = sprintf(
				'Could not instantiate object of type "%1$s" from factory of type: "%2$s".%3$s',
				$interface_name,
				gettype( $factory ),
				$reason
			);
		} elseif ( is_string( $factory ) ) {
			$message = sprintf(
				'Could not instantiate object of type "%1$s" from class name: "%2$s".%3$s',
				$interface_name,
				$factory,
				$reason
			);
		} else {
			$message = sprintf(
				'Could not instantiate object of type "%1$s" from invalid argument of type: "%2$s".%3$s',
				$interface_name,
				$factory,
				$reason
			);
		}

		return new static( $message, 0, $exception );
	}

	/**
	 * Create a new instance from a passed-in object that does not implement the
	 * correct interface.
	 *
	 * @since 0.3.0
	 *
	 * @param mixed  $factory        Class name or factory callable.
	 * @param string $interface_name Interface name that the object should have
	 *                               implemented.
	 * @return static Instance of an exception.
	 */
	public static function fromInvalidObject( $factory, $interface_name ) {
		$message = sprintf(
			'Could not instantiate object of type "%1$s", got "%2$s" instead.',
			$interface_name,
			$factory
		);
		return new static( $message );
	}
}
