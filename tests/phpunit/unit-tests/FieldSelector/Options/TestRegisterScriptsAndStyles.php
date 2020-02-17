<?php

namespace GFPDF\Tests\FieldSelector;

use GFPDF\Plugins\CoreBooster\FieldSelector\Options\RegisterScriptsAndStyles;
use WP_UnitTestCase;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TestRegisterScriptsAndStyles
 *
 * @package GFPDF\Tests\FieldSelector
 *
 * @group   selector
 */
class TestRegisterScriptsAndStyles extends WP_UnitTestCase {

	/**
	 * @var RegisterScriptsAndStyles
	 * @since 1.1
	 */
	private $class;

	/**
	 * @var int
	 * @since 1.1
	 */
	private $form_id;

	/**
	 * @since 1.1
	 */
	public function setUp() {

		$this->form_id = \GFAPI::add_form( json_decode( file_get_contents( __DIR__ . '/../../../json/products.json' ), true ) );

		$_GET['page'] = 'gfpdf-';
		$_GET['id']   = $this->form_id;
		$_GET['pid']  = 0;

		$this->class = new RegisterScriptsAndStyles( \GPDFAPI::get_misc_class(), \GPDFAPI::get_form_class() );
		$this->class->init();
	}

	/**
	 * @since 1.1
	 */
	public function test_admin_assets() {
		global $wp_scripts;

		$this->class->load_admin_assets();

		$this->assertArrayNotHasKey( 'gfpdf_js_friendly_multi_select', $wp_scripts->registered );
		$this->assertArrayNotHasKey( 'gfpdf_js_friendly_multi_select_initialize', $wp_scripts->registered );

		set_current_screen( 'dashboard' );
		$this->class->load_admin_assets();

		$this->assertArrayHasKey( 'gfpdf_js_friendly_multi_select', $wp_scripts->registered );
		$this->assertArrayHasKey( 'gfpdf_js_friendly_multi_select_initialize', $wp_scripts->registered );
	}

	/**
	 * @since 1.1
	 */
	public function test_get_localized_data() {
		$data = $this->class->get_localized_data( $this->form_id );

		$this->assertArrayHasKey( 'form', $data );
		$this->assertArrayHasKey( 'lang', $data );

		$this->assertEquals( 'product', $data['form'][1] );
	}
}