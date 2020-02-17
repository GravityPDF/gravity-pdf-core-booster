<?php

namespace GFPDF\Plugins\CoreBooster\ProductTable\Options;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;
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
 * Class DisableProductTable
 *
 * @package GFPDF\Plugins\CoreBooster\ProductTable\Options
 */
class DisableProductTable implements Helper_Interface_Actions {

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
		add_action( 'gfpdf_pre_html_fields', [ $this, 'apply_settings' ], 10, 2 );
		add_action( 'gfpdf_post_html_fields', [ $this, 'reset_settings' ], 10, 2 );
	}

	/**
	 * If the 'group_product_fields' setting is set to No we'll disable the product table
	 *
	 * @param array $entry
	 * @param array $settings
	 *
	 * @since 1.0
	 */
	public function apply_settings( $entry, $settings ) {
		$settings = $settings['settings'];

		if ( isset( $settings['group_product_fields'] ) ) {

			if ( $settings['group_product_fields'] === 'No' ) {
				$this->logger->notice( 'Ungroup products from table in PDF' );

				add_filter( 'gfpdf_current_pdf_configuration', [ $this, 'disable_product_table' ] );
			}

			if ( $settings['group_product_fields'] === 'Yes' ) {
				$this->logger->notice( 'Ungroup products from table in PDF' );

				add_filter( 'gfpdf_current_pdf_configuration', [ $this, 'enable_product_table' ] );
			}

			if ( $settings['group_product_fields'] === 'Disable' ) {
				$this->logger->notice( 'Remove product table in PDF' );

				add_filter( 'gfpdf_disable_product_table', '__return_true' );
			}
		}


	}

	/**
	 * Enable the product table in the PDF in show each product field individually
	 *
	 * @param array $config
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function enable_product_table( $config ) {
		$config['meta']['individual_products'] = false;

		return $config;
	}

	/**
	 * Disable the product table in the PDF in show each product field individually
	 *
	 * @param array $config
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function disable_product_table( $config ) {
		$config['meta']['individual_products'] = true;

		return $config;
	}

	/**
	 * Remove the filter that alters the product table
	 *
	 * @since 1.0
	 */
	public function reset_settings() {
		remove_filter( 'gfpdf_current_pdf_configuration', [ $this, 'disable_product_table' ] );
		remove_filter( 'gfpdf_current_pdf_configuration', [ $this, 'enable_product_table' ] );
		remove_filter( 'gfpdf_disable_product_table', '__return_true' );
	}
}