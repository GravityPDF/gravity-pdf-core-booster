<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields;

use GFPDF\Helper\Fields\Field_Radio;
use GFPDF\Helper\Helper_Abstract_Fields;

/**
 * Gravity Forms Field
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2018, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Core Booster.

    Copyright (C) 2018, Blue Liquid Designs

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
 * @since 1.0
 */
class AllRadio extends Field_Radio {

	/**
	 * Include all checkbox options in the list and tick the ones that were selected
	 *
	 * @param string $value
	 * @param bool   $label
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function html( $value = '', $label = true ) {
		$selected_item = ($value) ? $value : $this->value();
		$field_choices = $this->field->choices;

		/* Add Other option if present */
		if ( isset( $selected_item['value'] ) && strlen( $selected_item['value'] ) > 0 && $this->selected_is_other( $field_choices, $selected_item['value'] ) ) {
			$field_choices[] = [
				'text'  => $selected_item['value'],
				'value' => $selected_item['value'],
			];
		}

		$field_choices = apply_filters( 'gfpdf_radio_field_choices', $field_choices, $this );
		$html          = '<ul class="checked radio radio-show-all-options">';
		foreach ( $field_choices as $key => $option ) {
			$html .= $this->get_option_markup( $option, $key, $selected_item['value'] );
		}

		$html .= '</ul>';

		return ( $label ) ? Helper_Abstract_Fields::html( $html, $label ) : $html;
	}

	/**
	 * Check if the selected value isn't on the list (i.e the "Other" option)
	 *
	 * @param array  $choices
	 * @param string $selected
	 *
	 * @return bool Returns true if the selection is an "Other" option, or false otherwise
	 *
	 * @since 1.0
	 */
	private function selected_is_other( $choices, $selected ) {
		foreach ( $choices as $option ) {
			if ( wp_kses_post( $option['value'] ) === $selected ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Generate the radio item markup for a single option
	 *
	 * @param array  $option The current option 'text' and 'value'
	 * @param string $key
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	private function get_option_markup( $option, $key, $selected ) {
		$value            = apply_filters( 'gfpdf_show_field_value', false, $this->field, $option ); /* Set to `true` to show a field's value instead of the label */
		$sanitized_option = ( $value ) ? $option['value'] : $option['text'];
		$checked          = ( wp_kses_post( $option['value'] ) === $selected ) ? '&#9746;' : '&#9744;';

		return "<li id='field-{$this->field->id}-option-$key'>
				<span style='font-size: 125%;'>$checked</span> $sanitized_option
				</li>";
	}
}
