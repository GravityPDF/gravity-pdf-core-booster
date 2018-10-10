<?php

namespace GFPDF\Plugins\CoreBooster\ProductTable\Options;

use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;
use Monolog\Logger;

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
 * Class AddFields
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedLabels\Options
 */
class AddFields implements Helper_Interface_Filters {

	/**
	 * @var DoesTemplateHaveGroup
	 *
	 * @since 1.0
	 */
	private $group_checker;

	/**
	 * Holds our log class
	 *
	 * @var \Monolog\Logger
	 *
	 * @since 1.0
	 */
	protected $log;

	/**
	 * AddFields constructor.
	 *
	 * @param DoesTemplateHaveGroup $group_checker
	 * @param Logger                $log
	 *
	 * @since 1.0
	 */
	public function __construct( DoesTemplateHaveGroup $group_checker, Logger $log ) {
		$this->group_checker = $group_checker;
		$this->log           = $log;
	}


	/**
	 * Initialise our module
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_form_settings_custom_appearance', [ $this, 'add_template_option' ], 9999 );
	}

	/**
	 * Include the field label settings for Core and Universal templates
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function add_template_option( $settings ) {

		$override          = apply_filters( 'gfpdf_override_product_table', false, $settings ); /* Change this to true to override the core / universal check */
		$exclude_templates = apply_filters( 'gfpdf_excluded_templates', [
			'gpdf-cellulose',
			'gpdf-colossus',
		], $settings, 'product-field' ); /* Exclude this option for specific templates */

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates ) && ( $override || $this->group_checker->has_group() ) ) {
			$settings['group_product_fields'] = [
				'id'      => 'group_product_fields',
				'name'    => esc_html__( 'Group Products?', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes'     => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'      => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'Yes',
				'tooltip' => '<h6>' . esc_html__( 'Group Products?', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'When enabled, your product fields are all grouped at the end of the PDF in a formatted table (like the Gravity Forms entry details page).', 'gravity-pdf-core-booster' ),
			];

			/* Add disable product feature if Gravity PDF a specific version */
			if ( version_compare( PDF_EXTENDED_VERSION, '5.1.0', '>=' ) ) {
				$settings['group_product_fields']['options']['Disable'] = esc_html__( 'No Products', 'gravity-pdf-core-booster' );
			}

			$this->log->notice( 'Add "group_product_fields" field to settings' );
		}

		return $settings;
	}
}
