<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Options;

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
	 * Include the field option settings for Core and Universal templates
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function add_template_option( $settings ) {

		$override          = apply_filters( 'gfpdf_override_enhanced_options_fields', false, $settings ); /* Change this to true to override the core / universal check */
		$exclude_templates = apply_filters( 'gfpdf_excluded_templates', [], $settings, 'enhanced-options' ); /* Exclude this option for specific templates */

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates, true ) && ( $override || $this->group_checker->has_group() ) ) {
			$settings['show_all_options'] = [
				'id'      => 'show_all_options',
				'name'    => esc_html__( 'Show Field Options', 'gravity-pdf-core-booster' ),
				'type'    => 'multicheck',
				'options' => [
					'Radio'       => esc_html__( 'Show all options for Radio Fields', 'gravity-pdf-core-booster' ),
					'Checkbox'    => esc_html__( 'Show all options for Checkbox Fields', 'gravity-pdf-core-booster' ),
					'Select'      => esc_html__( 'Show all options for Select Fields', 'gravity-pdf-core-booster' ),
					'Multiselect' => esc_html__( 'Show all options for Multiselect Fields', 'gravity-pdf-core-booster' ),
				],
				'tooltip' => '<h6>' . esc_html__( 'Show Field Options', 'gravity-forms-pdf-extended' ) . '</h6>' . esc_html__( 'Controls whether Select, Radio, Multiselect and Checkbox fields will show all available options with the selected items checked in the PDF.', 'gravity-pdf-core-booster' ),
			];

			$settings['option_label_or_value'] = [
				'id'      => 'option_label_or_value',
				'name'    => esc_html__( 'Option Field Display', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Label' => esc_html__( 'Show Label', 'gravity-pdf-core-booster' ),
					'Value' => esc_html__( 'Show Value', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'Label',
				'tooltip' => '<h6>' . esc_html__( 'Option Field Display', 'gravity-forms-pdf-extended' ) . '</h6>' . esc_html__( 'Controls whether Select, Radio, Multiselect and Checkbox fields will show the selected option label or value in the PDF.', 'gravity-pdf-core-booster' ),
			];

			$this->logger->notice( 'Add "show_all_options" and "option_label_or_value" fields to settings' );
		}

		return $settings;
	}
}
