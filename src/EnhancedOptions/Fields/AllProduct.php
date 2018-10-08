<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields;

use GFPDF\Helper\Fields\Field_Product;
use GFPDF\Helper\Helper_Abstract_Fields;

/**
 * Gravity Forms Field
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2017, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Core Booster.

    Copyright (C) 2017, Blue Liquid Designs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Controls the display and output of the Checkbox HTML
 *
 * @since 1.2
 */
class AllProduct extends Field_Product {

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
		$selected_item = ($value) ? $value : $this->value();

		$html = '<ul class="checked product product-show-all-options">';
		foreach ( $this->field->choices as $key => $option ) {
			$html .= $this->get_option_markup( $option, $key, $selected_item['name'] );
		}

		$html .= '</ul>';

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
				<span style='font-size: 125%;'>$checked</span> $sanitized_option
				</li>";
	}
}
