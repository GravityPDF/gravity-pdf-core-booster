<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Styles;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2019, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
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
 * Class AddStyles
 *
 * @package GFPDF\Plugins\EnhancedOptions\Style
 */
class AddStyles implements Helper_Interface_Actions {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

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
		add_action( 'gfpdf_core_template', [ $this, 'add_styles' ] );
	}

	/**
	 * Include global CSS styles for Gravity PDF templates
	 *
	 * @since 1.0
	 */
	public function add_styles() {
		$this->logger->notice( 'Include Global PDF CSS for Enhanced Options' );

		echo '<style>' .
		     file_get_contents( __DIR__ . '/enhanced-option-selector-pdf-styles.css' ) .
		     '</style>';
	}
}