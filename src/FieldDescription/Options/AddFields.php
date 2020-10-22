<?php

namespace GFPDF\Plugins\CoreBooster\FieldDescription\Options;

use GFPDF\Helper\Helper_Trait_Logger;
use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;
use Monolog\Logger;

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

		$override = apply_filters( 'gfpdf_override_field_descriptions', false, $settings ); /* Change this to true to override the core / universal check */

		if ( $override || $this->group_checker->has_group() ) {
			$settings['include_field_description'] = [
				'id'      => 'include_field_description',
				'name'    => esc_html__( 'Show Field Description?', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes' => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'  => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'No',
				'tooltip' => '<h6>' . esc_html__( 'Show Field Description', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'When enabled, the field description will be displayed in the PDF. The description is placed above or below the user response, depending on the "Description placement" option found in Form Settings.', 'gravity-pdf-core-booster' ),
			];

			if ( version_compare( PDF_EXTENDED_VERSION, '6.0.0-beta1', '>=' ) ) {
				$settings['include_field_description']['type'] = 'toggle';
				unset( $settings['include_field_description']['options'] );
			}

			$this->logger->notice( 'Add "include_field_description" field to settings' );
		}

		return $settings;
	}
}
