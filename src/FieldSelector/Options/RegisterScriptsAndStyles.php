<?php

namespace GFPDF\Plugins\CoreBooster\FieldSelector\Options;

use GFPDF\Helper\Helper_Form;
use GFPDF\Helper\Helper_Misc;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2018, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
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
 * Class RegisterScriptsAndStyles
 *
 * @package GFPDF\Plugins\CoreBooster\FieldSelector\Options
 */
class RegisterScriptsAndStyles {

	/**
	 * @var Helper_Misc
	 *
	 * @since 1.1
	 */
	private $misc;

	/**
	 * @var Helper_Form
	 *
	 * @since 1.1
	 */
	private $gform;

	/**
	 * AddFields constructor.
	 *
	 * @param Helper_Misc $misc
	 * @param Helper_Form $gform
	 *
	 * @since 1.1
	 */
	public function __construct( Helper_Misc $misc, Helper_Form $gform ) {
		$this->misc  = $misc;
		$this->gform = $gform;
	}

	/**
	 * Initialise our module
	 *
	 * @since 1.1
	 */
	public function init() {
		$this->add_action();
	}

	/*
	 * @since 1.1
	 */
	public function add_action() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
	}

	/**
	 * @since 1.1
	 */
	public function load_admin_assets() {
		if ( $this->misc->is_gfpdf_page() ) {
			$form_id = ( isset( $_GET['id'] ) ) ? (int) $_GET['id'] : false;
			$pdf_id  = ( isset( $_GET['pid'] ) ) ? $_GET['pid'] : false;

			if ( $pdf_id !== false ) {
				$version = GFPDF_CORE_BOOSTER_VERSION;

				wp_enqueue_script( 'gfpdf_js_friendly_multi_select', plugins_url( 'dist/multi-select/js/jquery.multi-select-gpdf.js', GFPDF_CORE_BOOSTER_FILE ), [ 'jquery' ], $version );
				wp_enqueue_script( 'gfpdf_js_friendly_multi_select_initialize', plugins_url( 'dist/js/friendly-multi-select.js', GFPDF_CORE_BOOSTER_FILE ), [ 'jquery', 'gfpdf_js_friendly_multi_select' ], $version );
				wp_localize_script( 'gfpdf_js_friendly_multi_select_initialize', 'GPDFCOREBOOSTER', $this->get_form_fields( $form_id ) );

				wp_enqueue_style( 'gfpdf_css_friendly_multi_select', plugins_url( 'dist/multi-select/css/multi-select.dist.css', GFPDF_CORE_BOOSTER_FILE ), [], $version );
				wp_enqueue_style( 'gfpdf_css_friendly_multi_select_customize', plugins_url( 'dist/css/friendly-multi-select.css', GFPDF_CORE_BOOSTER_FILE ), [ 'gfpdf_css_friendly_multi_select' ], $version );
			}
		}
	}

	/**
	 * Get the
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	protected function get_form_fields( $form_id ) {
		$form = $this->gform->get_form( $form_id );

		if ( $form === null ) {
			return [];
		}

		$fields = [];
		foreach ( $form['fields'] as $field ) {
			$fields[ $field->id ] = $field->type;
		}

		return $fields;
	}
}
