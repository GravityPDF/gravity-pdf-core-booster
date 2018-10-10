<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Options;

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

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates ) && ( $override || $this->group_checker->has_group() ) ) {
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

			$this->log->notice( 'Add "show_all_options" and "option_label_or_value" fields to settings' );
		}

		return $settings;
	}
}
