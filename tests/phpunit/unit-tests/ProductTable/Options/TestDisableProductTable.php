<?php

namespace GFPDF\Tests\ProductTable;

use GFPDF\Plugins\CoreBooster\ProductTable\Options\DisableProductTable;
use WP_UnitTestCase;


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
 * Class TestDisableProductTable
 *
 * @package GFPDF\Tests\EnhancedLabels
 *
 * @group   producttable
 */
class TestDisableProductTable extends WP_UnitTestCase {

	/**
	 * @var DisableProductTable
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new DisableProductTable();
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_actions() {
		$this->assertEquals( 10, has_action( 'gfpdf_pre_html_fields', [
			$this->class,
			'apply_settings',
		] ) );

		$this->assertEquals( 10, has_action( 'gfpdf_post_html_fields', [
			$this->class,
			'reset_settings',
		] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_settings() {
		$this->class->apply_settings( [], [ 'settings' => [ 'group_product_fields' => 'No' ] ] );
		$this->assertEquals( 10, has_action( 'gfpdf_current_pdf_configuration', [ $this->class, 'disable_product_table' ] ) );

		$this->class->reset_settings();
		$this->assertFalse( has_action( 'gfpdf_current_pdf_configuration', [ $this->class, 'disable_product_table' ] ) );

		$this->class->apply_settings( [], [ 'settings' => [ 'group_product_fields' => 'Disable' ] ] );
		$this->assertEquals( 10, has_action( 'gfpdf_disable_product_table', '__return_true' ) );
		$this->class->reset_settings();
		$this->assertFalse( has_action( 'gfpdf_disable_product_table', '__return_true' ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_disable_product_table() {
		$results = $this->class->disable_product_table( [ 'meta' => [] ] );
		$this->assertTrue( $results['meta']['individual_products'] );
	}
}