<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Options;

use GFPDF\Helper\Helper_Abstract_Fields;
use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllCheckbox;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllMultiselect;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllProductOptions;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllRadio;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllSelect;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllProduct;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllOptions;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllShipping;
use GFPDF\Helper\Fields\Field_Products;
use Monolog\Logger;
use GF_Field;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2017, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Core Booster.

    Copyright (C) 2017, Blue Liquid Designs

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
 * Class DisplayAllOptions
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedLabels\Options
 */
class DisplayAllOptions implements Helper_Interface_Actions, Helper_Interface_Filters {

	/**
	 * @var array The current PDF Settings
	 *
	 * @since 1.0
	 */
	private $settings;

	/**
	 * Holds our log class
	 *
	 * @var \Monolog\Logger
	 *
	 * @since 1.0
	 */
	protected $log;

	/**
	 * DisplayAllOptions constructor.
	 *
	 * @param Logger $log
	 *
	 * @since 1.0
	 */
	public function __construct( Logger $log ) {
		$this->log = $log;
	}

	/**
	 * Initialise our module
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'gfpdf_pre_html_fields', [ $this, 'save_settings' ], 10, 2 );
		add_action( 'gfpdf_post_html_fields', [ $this, 'reset_settings' ], 10, 2 );
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_field_class', [ $this, 'maybe_autoload_class' ], 10, 3 );
	}

	/**
	 * Save the PDF Settings for later use
	 *
	 * @param array $entry
	 * @param array $settings
	 *
	 * @since 1.0
	 */
	public function save_settings( $entry, $settings ) {
		$this->settings = $settings['settings'];
	}

	/**
	 * Get the current saved PDF settings
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Remove the current saved PDF Settings
	 *
	 * @since 1.0
	 */
	public function reset_settings() {
		$this->settings = null;
	}

	/**
	 * Check which settings are activate and render the radio/select/checkbox/multiselect fields with our
	 * modified classes, if needed
	 *
	 * @param Helper_Abstract_Fields $class
	 * @param object                 $field
	 * @param array                  $entry
	 *
	 * @return Helper_Abstract_Fields
	 *
	 * @since 1.0
	 */
	public function maybe_autoload_class( $class, $field, $entry ) {

		/* Ensure the settings have been set and we aren't too early in the process */
		if ( isset( $this->settings['show_all_options'] ) && is_array( $this->settings['show_all_options'] ) ) {
			$option_config = $this->settings['show_all_options'];
			$products = new Field_Products( new GF_Field(), $entry, $class->gform, $class->misc );

			/*
			 * Override the Product Select field HTML processing if configured to do so
			 */
			if ( $field->type === 'product' && $field->get_input_type() === 'select' && isset( $option_config['Select'] ) ) {
				$this->log->notice( 'Override Product Select field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllProduct( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Product Radio field HTML processing if configured to do so
			 */
			if ( $field->type === 'product' && $field->get_input_type() === 'radio' && isset( $option_config['Radio'] ) ) {
				$this->log->notice( 'Override Product Radio field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllProduct( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Option Select field HTML processing if configured to do so
			 */
			if ( $field->type === 'option' && $field->get_input_type() === 'select' && isset( $option_config['Select'] ) ) {
				$this->log->notice( 'Override Option Select field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllOptions( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Option Radio field HTML processing if configured to do so
			 */
			if ( $field->type === 'option' && $field->get_input_type() === 'radio' && isset( $option_config['Radio'] ) ) {
				$this->log->notice( 'Override Option Radio field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllOptions( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Option Checkbox field HTML processing if configured to do so
			 */
			if ( $field->type === 'option' && $field->get_input_type() === 'checkbox' && isset( $option_config['Checkbox'] ) ) {
				$this->log->notice( 'Override Option Radio field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllOptions( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Shipping Checkbox field HTML processing if configured to do so
			 */
			if ( $field->type === 'shipping' && $field->get_input_type() === 'select' && isset( $option_config['Select'] ) ) {
				$this->log->notice( 'Override Shipping Select field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllShipping( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override the Shipping Radio field HTML processing if configured to do so
			 */
			if ( $field->type === 'shipping' && $field->get_input_type() === 'radio' && isset( $option_config['Radio'] ) ) {
				$this->log->notice( 'Override Shipping Radio field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				$class = new AllShipping( $field, $entry, $class->gform, $class->misc );
				$class->set_products( $products );

				return $class;
			}

			/*
			 * Override Radio field HTML processing if configured to do so
			 */
			if ( $field->get_input_type() === 'radio' && isset( $option_config['Radio'] ) ) {
				$this->log->notice( 'Override Radio field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				return new AllRadio( $field, $entry, $class->gform, $class->misc );
			}

			/*
			 * Override Select field HTML processing if configured to do so
			 */
			if ( $field->get_input_type() === 'select' && isset( $option_config['Select'] ) ) {
				$this->log->notice( 'Override Select field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				return new AllSelect( $field, $entry, $class->gform, $class->misc );
			}

			/*
			 * Override Checkbox field HTML processing if configured to do so
			 */
			if ( $field->get_input_type() === 'checkbox' && isset( $option_config['Checkbox'] ) ) {
				$this->log->notice( 'Override Checkbox field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				return new AllCheckbox( $field, $entry, $class->gform, $class->misc );
			}

			/*
			 * Override Multiselect field HTML processing if configured to do so
			 */
			if ( $field->get_input_type() === 'multiselect' && isset( $option_config['Multiselect'] ) ) {
				$this->log->notice(
					'Override Multiselect field generator class', [
					'f_id'    => $field->id,
					'f_label' => $field->label,
				] );

				return new AllMultiselect( $field, $entry, $class->gform, $class->misc );
			}
		}

		return $class;
	}
}