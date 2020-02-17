<?php

namespace GFPDF\Plugins\CoreBooster\Notes\Options;

use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AddFields
 *
 * @package GFPDF\Plugins\CoreBooster\Notes\Options
 */
class AddFields implements Helper_Interface_Filters {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * @var DoesTemplateHaveGroup
	 *
	 * @since 1.1
	 */
	private $group_checker;

	/**
	 * AddFields constructor.
	 *
	 * @param DoesTemplateHaveGroup $group_checker
	 *
	 * @since 1.1
	 */
	public function __construct( DoesTemplateHaveGroup $group_checker ) {
		$this->group_checker = $group_checker;
	}

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
		add_filter( 'gfpdf_form_settings_custom_appearance', [ $this, 'add_template_option' ], 9999 );
	}

	/**
	 * Include the field option settings for Core and Universal templates
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.1
	 */
	public function add_template_option( $settings ) {

		$override          = apply_filters( 'gfpdf_override_notes_fields', false, $settings ); /* Change this to true to override the core / universal check */
		$exclude_templates = apply_filters( 'gfpdf_excluded_templates', [], $settings, 'notes' ); /* Exclude this option for specific templates */

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates ) && ( $override || $this->group_checker->has_group() ) ) {
			$settings['display_entry_notes'] = [
				'id'      => 'display_entry_notes',
				'name'    => esc_html__( 'Show Entry Notes?', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes' => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'  => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'No',
				'tooltip' => '<h6>' . esc_html__( 'Show Entry Notes', 'gravity-pdf-core-booster' ) . '</h6>' . sprintf( esc_html__( 'When enabled, any notes associated with the entry will be appended to the end of the PDF.', 'gravity-pdf-core-booster' ), '<code>', '</code>' ),
			];

			$this->logger->notice( 'Add display_entry_notes" field to settings' );
		}

		return $settings;
	}
}
