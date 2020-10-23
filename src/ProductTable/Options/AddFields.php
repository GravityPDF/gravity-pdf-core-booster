<?php

namespace GFPDF\Plugins\CoreBooster\ProductTable\Options;

use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AddFields
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedLabels\Options
 */
class AddFields implements Helper_Interface_Filters {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * @var DoesTemplateHaveGroup
	 *
	 * @since 1.0
	 */
	private $group_checker;

	/**
	 * AddFields constructor.
	 *
	 * @param DoesTemplateHaveGroup $group_checker
	 *
	 * @since 1.0
	 */
	public function __construct( DoesTemplateHaveGroup $group_checker ) {
		$this->group_checker = $group_checker;
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

		$override               = apply_filters( 'gfpdf_override_product_table', false, $settings ); /* Change this to true to override the core / universal check */
		$exclude_templates_list = ( version_compare( PDF_EXTENDED_VERSION, '5.1.0', '>=' ) ) ? [] : [ 'gpdf-cellulose', 'gpdf-leo' ];
		$exclude_templates      = apply_filters( 'gfpdf_excluded_templates', $exclude_templates_list, $settings, 'product-field' ); /* Exclude this option for specific templates */

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates, true ) && ( $override || $this->group_checker->has_group() ) ) {
			$settings['group_product_fields'] = [
				'id'      => 'group_product_fields',
				'name'    => esc_html__( 'Group Products?', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes' => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'  => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'Yes',
				'tooltip' => '<h6>' . esc_html__( 'Group Products?', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'When enabled, your product fields are all grouped at the end of the PDF in a formatted table (like the Gravity Forms entry details page).', 'gravity-pdf-core-booster' ),
			];

			/* Add disable product feature if Gravity PDF a specific version */
			if ( version_compare( PDF_EXTENDED_VERSION, '5.1.0', '>=' ) ) {
				$settings['group_product_fields']['options']['Disable'] = esc_html__( 'No Products', 'gravity-pdf-core-booster' );
			}

			$this->logger->notice( 'Add "group_product_fields" field to settings' );
		}

		return $settings;
	}
}
