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
	 * Shortcode handler.
	 *
	 * @since 0.1.0
	 *
	 * @var ShortcodeInterface
	 */
	protected $shortcode;

	/**
	 * Instantiate the shortcode attributes parser object
	 *
	 * @since 0.1.0
	 *
	 * @param ShortcodeInterface $shortcode  Name of the shortcode handler.
	 * @param ConfigInterface    $config     Configuration array to
	 *                                       parametrize the shortcode
	 *                                       attributes.
	 * @param string             $config_key Optional. Key of the
	 *                                       configuration subtree.
	 * @throws RuntimeException If the config could not be processed.
	 */
	public function __construct(
		ShortcodeInterface $shortcode,
		ConfigInterface $config,
		$config_key = null
	) {

		$this->processConfig( $config, $config_key );
		$this->shortcode = $shortcode;
	}

	/**
	 * Parse and validate the shortcode's attributes.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $atts Attributes passed to the shortcode.
	 * @return array       Validated attributes of the shortcode.
	 */
	public function parse_atts( $atts ) {
		$atts = \shortcode_atts(
			$this->default_atts(),
			$this->validated_atts( $atts ),
			$this->shortcode->get_tag()
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

		if ( array_key_exists( 'atts', $this->config ) ) {
			array_walk( $this->config['atts'],
				function ( $att_properties, $att_label ) use ( &$atts ) {
					$atts[ $att_label ] = $att_properties['default'];
				}
			);
		}

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

		if ( array_key_exists( 'atts', $this->config ) ) {
			array_walk( $this->config['atts'],
				function ( $att_properties, $att_label ) use ( &$atts ) {
					if ( array_key_exists( $att_label, $atts ) ) {
						$validate_function  = $att_properties['validate'];
						$atts[ $att_label ] = $validate_function( $atts[ $att_label ] );
					}
				}
			);
		}

		return $atts;
	}
}
