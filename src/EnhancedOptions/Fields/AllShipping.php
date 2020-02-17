<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields;

use GFPDF\Helper\Fields\Field_Shipping;
use GFPDF\Helper\Helper_Abstract_Fields;

/**
 * Gravity Forms Field
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls the display and output of the Checkbox HTML
 *
 * @since 1.2
 */
class AllShipping extends Field_Shipping {

	/**
	 * Include all checkbox options in the list and tick the ones that were selected
	 *
	 * @param string $value
	 * @param bool   $label
	 *
	 * @return string
	 *
	 * @since 1.2
	 */
	public function html( $value = '', $label = true ) {
		$selected_item = ( $value ) ? $value : $this->value();
		$html          = '';

		if ( isset( $selected_item['shipping_formatted'] ) ) {
			$html = '<ul class="checked shipping shipping-show-all-options">';
			foreach ( $this->field->choices as $key => $option ) {
				$html .= $this->get_option_markup( $option, $key, $selected_item['shipping_name'] );
			}

			$html .= '</ul>';
		}

		return ( $label ) ? Helper_Abstract_Fields::html( $html, $label ) : $html;
	}

	/**
	 * Generate the select item markup for a single option
	 *
	 * @param array  $option The current option 'text' and 'value'
	 * @param string $key
	 *
	 * @return string
	 *
	 * @since 1.2
	 */
	private function get_option_markup( $option, $key, $selected ) {
		$value            = apply_filters( 'gfpdf_show_field_value', false, $this->field, $option ); /* Set to `true` to show a field's value instead of the label */
		$sanitized_option = ( $value ) ? $option['value'] : $option['text'];
		$sanitized_option .= ' - ' . $option['price'];

		$option_name = ( $value ) ? $option['value'] : $option['text'];
		$checked     = ( esc_html( $option_name ) === $selected ) ? '&#9746;' : '&#9744;';

		return "<li id='field-{$this->field->id}-option-$key'>
				<span style='font-size: 125%; font-family: DejavuSansCondensed'>$checked</span> $sanitized_option
				</li>";
	}
}
