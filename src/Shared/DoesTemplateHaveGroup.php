<?php

namespace GFPDF\Plugins\CoreBooster\Shared;

use GFPDF\Helper\Helper_Templates;
use GFPDF\Helper\Helper_Trait_Logger;
use GFPDF\Model\Model_Form_Settings;
use Monolog\Logger;

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
 * Class DoesTemplateHaveGroup
 *
 * @package GFPDF\Plugins\CoreBooster\Shared
 */
class DoesTemplateHaveGroup {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

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
			$this->logger->notice( 'The PDF Template is in a core or universal group.', $template_info );

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
			$this->logger->notice( 'The template name was retreived from POST data', $_POST );

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
