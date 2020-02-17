<?php

namespace GFPDF\Tests\EnhancedOptions;

use GFPDF\Plugins\CoreBooster\EnhancedOptions\Styles\AddStyles;

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
 * Class TestAddStyles
 *
 * @package GFPDF\Tests\EnhancedOptions
 *
 * @group   options
 */
class TestAddStyles extends WP_UnitTestCase {

	/**
	 * @var AddStyles
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new AddStyles();
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_actions() {
		$this->assertEquals( 10, has_action( 'gfpdf_core_template', [
			$this->class,
			'add_styles',
		] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_add_styles() {
		ob_start();
		$this->class->add_styles();
		$results = ob_get_clean();

		$this->assertNotFalse( strpos( $results, 'show-all-options li' ) );
	}
}