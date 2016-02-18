<?php
/**
 * Shortcode Attributes Parser Base Implementation.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Exception\RuntimeException;

/**
 * Base implementation of the Shortcode Attributes Parser Interface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class ShortcodeAttsParser implements ShortcodeAttsParserInterface {

	use ConfigTrait;

	/**
	 * Instantiate the shortcode attributes parser object
	 *
	 * @since 0.1.0
	 *
	 * @param ConfigInterface $config        Configuration array to
	 *                                       parametrize the shortcode
	 *                                       attributes.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct( ConfigInterface $config ) {
		$this->processConfig( $config );
	}

	/**
	 * Parse and validate the shortcode's attributes.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $atts Attributes passed to the shortcode.
	 * @param string $tag  Tag of the shortcode.
	 * @return array       Validated attributes of the shortcode.
	 */
	public function parse_atts( $atts, $tag ) {
		$atts = \shortcode_atts(
			$this->default_atts(),
			$this->validated_atts( $atts ),
			$tag
		);

		return $atts;
	}

	/**
	 * Return an array of default attributes read from the configuration array.
	 *
	 * @since 0.1.0
	 *
	 * @return array Default attributes.
	 */
	protected function default_atts() {

		$atts = array();

		if ( ! $this->hasConfigKey( 'atts' ) ) {
			return $atts;
		}

		$atts_config = $this->getConfigKey( 'atts' );
		array_walk( $atts_config,
			function ( $att_properties, $att_label ) use ( &$atts ) {
				$atts[ $att_label ] = $att_properties['default'];
			}
		);

		return $atts;
	}

	/**
	 * Return an array of validated attributes checked against the
	 * configuration array.
	 *
	 * @since 0.1.0
	 *
	 * @param array $atts Attributes that were passed to the shortcode.
	 * @return array Validated attributes.
	 */
	protected function validated_atts( $atts ) {

		if ( ! $this->hasConfigKey( 'atts' ) ) {
			return $atts;
		}

		$atts_config = $this->getConfigKey( 'atts' );
		array_walk( $atts_config,
			function ( $att_properties, $att_label ) use ( &$atts ) {
				if ( array_key_exists( $att_label, $atts ) ) {
					$validate_function  = $att_properties['validate'];
					$atts[ $att_label ] = $validate_function( $atts[ $att_label ] );
				}
			}
		);

		return $atts;
	}
}
