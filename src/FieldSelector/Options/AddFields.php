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
			$settings['form_field_selector'] = [
				'id'         => 'form_field_selector',
				'name'       => esc_html__( 'Display Fields', 'gravity-pdf-core-booster' ),
				'type'       => 'select',
				'inputClass' => 'gfpdf-friendly-select',
				'multiple'   => true,
				'options'    => $this->get_form_fields(),
				'tooltip'    => '<h6>' . esc_html__( 'Display Fields', 'gravity-pdf-core-booster' ) . '</h6>' . sprintf( esc_html__( 'Control which Gravity Forms fields to display in the PDF. To use Product fields, set %1$sGroup Products%2$s to %1$sNo%2$s. To control HTML fields, enable the %1$sShow HTML Fields%2$s setting.', 'gravity-pdf-core-booster' ), '<em>', '</em>' ),
			];

			$settings['form_field_selector_enabled'] = [
				'id'    => 'form_field_selector_enabled',
				'type'  => 'hidden',
				'class' => 'gfpdf-hidden',
				'std'   => '0',
			];

			/* If we couldn't get the current fields, disable this feature */
			if ( count( $settings['form_field_selector']['options'] ) === 0 ) {
				unset( $settings['form_field_selector'] );
				unset( $settings['form_field_selector_enabled'] );
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
			if ( in_array( $field->type, apply_filters( 'gfpdf_form_field_selector_skip_field', [ 'page', 'captcha' ], $form ) ) ) {
				continue;
			}

			$choices[ $field->id ] = esc_html( 'ID' . $field->id . ': ' . $field->label );
		}

		return $choices;
	}
}
