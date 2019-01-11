<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Options;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2019, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Core Booster.

    Copyright (c) 2019, Blue Liquid Designs

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
 * Class DisplayLabelOrValue
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedLabels\Options
 */
class DisplayLabelOrValue implements Helper_Interface_Actions {

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
	 * Apply our filter to show the option value if our saved setting value is correct
	 *
	 * @param array $entry
	 * @param array $settings
	 *
	 * @since 1.0
	 */
	public function apply_settings( $entry, $settings ) {
		$settings = $settings['settings'];

		if ( isset( $settings['option_label_or_value'] ) && $settings['option_label_or_value'] === 'Value' ) {
			$this->logger->notice( 'Show field value instead of label in PDF' );

			add_filter( 'gfpdf_show_field_value', [ $this, 'maybe_show_field_value' ], 10, 2 );
		}
	}

	/**
	 * Show the value for all field types except Survey
	 *
	 * @param bool $enable
	 * @param \GF_Field $field
	 *
	 * @return bool
	 *
	 * @since 1.1
	 */
	public function maybe_show_field_value( $enable, $field = null ) {
		if ( $field === null || ! in_array( $field->type, [ 'survey' ] ) ) {
			return true;
		}

		return $enable;
	}

	/**
	 * Remove the filter we added
	 *
	 * @since 1.0
	 */
	public function reset_settings() {
		remove_filter( 'gfpdf_show_field_value', [ $this, 'maybe_show_field_value' ] );
	}
}