<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedLabels\Options;

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

		$override = apply_filters( 'gfpdf_override_enhanced_label_fields', false, $settings ); /* Change this to true to override the core / universal check */

		if ( $override || $this->group_checker->has_group() ) {
			$settings['field_label_display'] = [
				'id'      => 'field_label_display',
				'name'    => esc_html__( 'Field Label Display', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Standard'    => esc_html__( 'Standard Label', 'gravity-pdf-core-booster' ),
					'Admin'       => esc_html__( 'Admin Label', 'gravity-pdf-core-booster' ),
					'Admin Empty' => esc_html__( 'Admin Label (if not empty)', 'gravity-pdf-core-booster' ),
					'No Label'    => esc_html__( 'No Label', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'Standard',
				'tooltip' => '<h6>' . esc_html__( 'Field Label Display', 'gravity-pdf-core-booster' ) . '</h6>' . sprintf( esc_html__( 'Control which label should be displayed for each  in the PDF. The option %sAdmin Label (if not empty)%s will fallback to the Standard Label display if no admin label is entered for a particular field.', 'gravity-pdf-core-booster' ), '<code>', '</code>' ),
			];

			$this->logger->notice( 'Add "field_label_display" field to settings' );
		}

		return $settings;
	}
}
