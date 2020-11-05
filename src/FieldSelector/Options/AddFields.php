<?php

namespace GFPDF\Plugins\CoreBooster\FieldSelector\Options;

use GFPDF\Helper\Helper_Form;
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
	 * @since 1.1
	 */
	private $group_checker;

	/**
	 * @var Helper_Form
	 *
	 * @since 1.1
	 */
	private $gform;

	/**
	 * AddFields constructor.
	 *
	 * @param DoesTemplateHaveGroup $group_checker
	 * @param Helper_Form           $gform
	 *
	 * @since 1.1
	 */
	public function __construct( DoesTemplateHaveGroup $group_checker, Helper_Form $gform ) {
		$this->group_checker = $group_checker;
		$this->gform         = $gform;
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
	 * Include the field label settings for Core and Universal templates
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.1
	 */
	public function add_template_option( $settings ) {

		$override = apply_filters( 'gfpdf_override_field_selector_fields', false, $settings ); /* Change this to true to override the core / universal check */

		if ( $override || $this->group_checker->has_group() ) {

			$settings['form_field_filter_fields'] = [
				'id'   => 'form_field_filter_fields',
				'type' => version_compare( PDF_EXTENDED_VERSION, '6.0.0-beta1', '>=' ) ? 'toggle' : 'checkbox',
				'name' => esc_html__( 'Filter Fields', 'gravity-pdf-core-booster' ),
				'desc' => esc_html__( 'Enable the ability to control which Gravity Forms fields get displayed in the PDF.', 'gravity-pdf-core-booster' ),
			];

			$settings['form_field_selector'] = [
				'id'         => 'form_field_selector',
				'name'       => esc_html__( 'Select fields to display in PDF', 'gravity-pdf-core-booster' ),
				'type'       => 'select',
				'inputClass' => 'gfpdf-friendly-select',
				'multiple'   => true,
				'options'    => $this->get_form_fields(),
				'desc'       => sprintf( esc_html__( 'Fields in the right hand column (labelled %1$sIncluded%2$s) will get displayed in the PDF. To use Product fields, set %1$sGroup Products%2$s setting to %1$sNo%2$s. To control HTML fields, enable the %1$sShow HTML Fields%2$s setting.', 'gravity-pdf-core-booster' ), '<em>', '</em>' ),
				'class'      => 'gfpdf-hidden',
			];

			$settings['form_field_selector_enabled'] = [
				'id'    => 'form_field_selector_enabled',
				'type'  => 'hidden',
				'class' => 'gfpdf-hidden',
				'std'   => '-1',
			];

			/* If we couldn't get the current fields, disable this feature */
			if ( count( $settings['form_field_selector']['options'] ) === 0 ) {
				unset( $settings['form_field_selector'] );
				unset( $settings['form_field_selector_enabled'] );
				unset( $settings['form_field_filter_fields'] );
			} else {
				$this->logger->notice( 'Add "form_field_selector" field to settings' );
			}
		}

		return $settings;
	}

	/**
	 * Gets all active fields for the current Gravity Form
	 *
	 * @return array
	 *
	 * @since 1.1
	 */
	protected function get_form_fields() {
		$form_id = ( isset( $_REQUEST['id'] ) ) ? (int) $_REQUEST['id'] : 0;
		$form    = $this->gform->get_form( $form_id );

		if ( ! is_array( $form ) || empty( $form['fields'] ) ) {
			return [];
		}

		$choices = [];
		foreach ( $form['fields'] as $field ) {
			if ( in_array( $field->type, apply_filters( 'gfpdf_form_field_selector_skip_field', [ 'page', 'captcha' ], $form ), true ) ) {
				continue;
			}

			$choices[ $field->id ] = esc_html( 'ID' . $field->id . ': ' . $field->label );
		}

		return $choices;
	}
}
