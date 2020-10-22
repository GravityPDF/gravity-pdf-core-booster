<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedOptions\Styles;

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
