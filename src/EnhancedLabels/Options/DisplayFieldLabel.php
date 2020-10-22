<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedLabels\Options;

use GFPDF\Helper\Helper_Interface_Actions;
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
 * Class DisplayFieldLabel
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedLabels\Options
 */
class DisplayFieldLabel implements Helper_Interface_Actions {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * Holds the user selection for the 'field_label_display' setting
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	protected $label_type;

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

		if ( isset( $settings['field_label_display'] ) && $settings['field_label_display'] !== 'Standard' ) {
			$this->label_type = $settings['field_label_display'];
			add_filter( 'gfpdf_field_label', [ $this, 'change_field_label_display' ], 10, 2 );
			add_filter( 'gfpdf_use_admin_label', [ $this, 'change_product_field_label_display' ] );
		}
	}

	/**
	 * Alter the current field label if the 'field_label_display' setting is changed
	 *
	 * @param string $label
	 * @param object $field
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function change_field_label_display( $label, $field ) {
		switch ( $this->label_type ) {
			case 'Admin':
				$this->logger->notice(
					'Show admin field label in PDFs',
					[
						'f_id'          => $field->id,
						'f_label'       => $field->label,
						'f_admin_label' => $field->adminLabel,
					]
				);
				return $field->adminLabel;
			break;

			case 'Admin Empty':
				$this->logger->notice(
					'Show admin field label in PDFs if not empty',
					[
						'f_id'          => $field->id,
						'f_label'       => $field->label,
						'f_admin_label' => $field->adminLabel,
					]
				);
				return ( strlen( $field->adminLabel ) === 0 ) ? $label : $field->adminLabel;
			break;

			case 'No Label':
				$this->logger->notice( 'Hide all field labels in PDF', [ 'f_id' => $field->id ] );
				return '';
			break;
		}

		return $label;
	}

	/**
	 * Tell the Product table to use the Admin Labels to match the Gravity Forms Product table
	 *
	 * @param bool $use_admin_label
	 *
	 * @return bool
	 *
	 * @since 1.1
	 */
	public function change_product_field_label_display( $use_admin_label ) {
		switch ( $this->label_type ) {
			case 'Admin':
			case 'Admin Empty':
				return true;
		}

		return $use_admin_label;
	}

	/**
	 * Remove the filter that alters the field label
	 *
	 * @since 1.0
	 */
	public function reset_settings() {
		remove_filter( 'gfpdf_field_label', [ $this, 'change_field_label_display' ] );
		remove_filter( 'gfpdf_use_admin_label', [ $this, 'change_product_field_label_display' ] );
	}
}
