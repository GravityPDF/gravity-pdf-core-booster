<?php

namespace GFPDF\Plugins\CoreBooster\Shared;

use GFPDF\Helper\Helper_Templates;
use GFPDF\Model\Model_Form_Settings;

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
    This file is part of Gravity PDF.

    Gravity PDF – Copyright (C) 2017, Blue Liquid Designs

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
 * Class DoesTemplateHaveGroup
 *
 * @package GFPDF\Plugins\CoreBooster\Options
 */
class DoesTemplateHaveGroup {

	/**
	 * @var Model_Form_Settings
	 *
	 * @since 1.0
	 */
	private $form_settings;

	/**
	 * @var Helper_Templates
	 *
	 * @since 1.0
	 */
	private $templates;

	/**
	 * AddFields constructor.
	 *
	 * @param Model_Form_Settings $form_settings
	 * @param Helper_Templates    $templates
	 *
	 * @since 1.0
	 */
	public function __construct( Model_Form_Settings $form_settings, Helper_Templates $templates ) {
		$this->form_settings = $form_settings;
		$this->templates     = $templates;
	}

	/**
	 * @param string $template_name
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function has_group( $template_name = '' ) {

		if ( $template_name === '' ) {
			$template_name = $this->get_template_name();
		}

		$template_info = $this->templates->get_template_info_by_id( $template_name );
		if ( $template_info['group'] === 'Core' || $template_info['group'] === 'Universal (Premium)' ) {
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 *
	 * @since 1.0
	 */
	public function get_template_name() {
		if ( $this->ajax_template_request() ) {
			return $_POST['template'];
		}

		return $this->form_settings->get_template_name_from_current_page();
	}

	/**
	 * @return bool
	 *
	 * @since 1.0
	 */
	private function ajax_template_request() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
		     && isset( $_POST['action'] ) && $_POST['action'] === 'gfpdf_get_template_fields'
		     && isset( $_POST['template'] )
		) {
			return true;
		}

		return false;
	}
}