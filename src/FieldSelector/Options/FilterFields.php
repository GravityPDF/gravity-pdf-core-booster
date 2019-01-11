<?php

namespace GFPDF\Plugins\CoreBooster\FieldSelector\Options;

use GFPDF\Helper\Helper_Form;
use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2019, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
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
 * Class FilterFields
 *
 * @package GFPDF\Plugins\CoreBooster\FieldSelector\Options
 */
class FilterFields implements Helper_Interface_Filters {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * Initialise our module
	 *
	 * @since 1.1
	 */
	public function init() {
		$this->add_filters();
	}

	/**
	 * @since 1.1
	 */
	public function add_filters() {
		add_filter( 'gfpdf_current_pdf_configuration', [ $this, 'disable_excluded_fields' ], 10 );
		add_filter( 'gfpdf_field_middleware', [ $this, 'filter_fields' ], 20, 5 );
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 *
	 * @since 1.1
	 */
	public function disable_excluded_fields( $config ) {
		if ( isset( $config['settings']['form_field_selector'] ) ) {
			$config['meta']['exclude'] = false;
		}

		return $config;
	}

	/**
	 * Filter out any fields not found in our Field Selector setting
	 *
	 * @param $filter
	 * @param $field
	 * @param $entry
	 * @param $form
	 * @param $config
	 *
	 * @return bool
	 *
	 * @since 1.1
	 */
	public function filter_fields( $filter, $field, $entry, $form, $config ) {
		/* All fields deselected so remove */
		if ( ! isset( $config['settings']['form_field_selector'] ) && isset( $config['settings']['form_field_selector_enabled'] ) ) {
			return true;
		}

		if ( isset( $config['settings']['form_field_selector'] ) && ! in_array( $field->id, (array) $config['settings']['form_field_selector'] ) ) {
			$this->logger->notice( 'Removing field %s from PDF display due to Form Field Selector option' );
			return true;
		}

		return $filter;
	}

}
