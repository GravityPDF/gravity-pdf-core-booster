<?php

namespace GFPDF\Plugins\CoreBooster\FieldDescription\Options;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
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
 * Class DisplayFieldDescription
 *
 * @package GFPDF\Plugins\CoreBooster\FieldDescription\Options
 */
class DisplayFieldDescription implements Helper_Interface_Actions {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * Initialise our module
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_actions();
	}

	/**
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'gfpdf_pre_html_fields', [ $this, 'apply_settings' ], 10, 2 );
		add_action( 'gfpdf_post_html_fields', [ $this, 'reset_settings' ], 10, 2 );
	}

	/**
	 * If the 'field_label_display' setting isn't Standard add filter to change the label format
	 *
	 * @param array $entry
	 * @param array $settings
	 *
	 * @since 1.0
	 */
	public function apply_settings( $entry, $settings ) {
		$settings = $settings['settings'];

		if ( isset( $settings['include_field_description'] ) && $settings['include_field_description'] === 'Yes' ) {
			$this->logger->notice( 'Show field descriptions in generated PDF.');

			add_filter( 'gfpdf_pdf_field_content', [ $this, 'add_field_description' ], 10, 4 );
		}
	}

	/**
	 * Display the current field description above or below the user response (if a description exists)
	 *
	 * @param string   $value The current field value
	 * @param GF_Field $field The Gravity Form field being processed
	 * @param array    $entry
	 * @param array    $form
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function add_field_description( $value, $field, $entry, $form ) {

		if ( strlen( $field->description ) > 0 ) {

			/* Get the descripion and wrap it in a DIV */
			$description = '<div class="gfpdf-field-description gfpdf-' . $field->get_input_type() . '">' . $field->description . '</div>';
			$description = wp_kses_post( $description );

			/* Include a spacer between the description and the user response */
			$spacer = apply_filters( 'gfpdf_description_spacer', '<div class="gfpdf_description_spacer">&mdash;</div>', $field, $entry, $form );

			return ( $field->is_description_above( $form ) ) ? $description . $spacer . $value : $value . $spacer . $description;
		}

		return $value;
	}

	/**
	 * Remove the filter that alters the field description
	 *
	 * @since 1.0
	 */
	public function reset_settings() {
		remove_filter( 'gfpdf_pdf_field_content', [ $this, 'add_field_description' ] );
	}
}