<?php

namespace GFPDF\Tests\EnhancedOptions;

use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllOptions;
use GFPDF\Helper\Fields\Field_Products;
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
 * Class TestAllOptionsCheckbox
 *
 * @package GFPDF\Tests\EnhancedOptions
 *
 * @group   options
 */
class TestAllOptionsCheckbox extends WP_UnitTestCase {

	/**
	 * @var AllProduct
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

		$form = \GFAPI::get_form( $this->form_id );

		$entry = [
			'form_id'  => $this->form_id,
			'currency' => 'USD',
			'id'       => 0,
			'1'        => '',
			'2'        => 'vFirst Choice2|4',
			'3'        => '',
			'4.1'      => 'vFirst Option4|10',
			'4.2'      => '',
			'4.3'      => 'vThird Option4|12',
			'5'        => '',
		];

		$this->class = new AllOptions( $form['fields'][3], $entry, \GPDFAPI::get_form_class(), \GPDFAPI::get_misc_class() );
		$this->class->set_products( new Field_Products( new \GF_Field(), $entry, \GPDFAPI::get_form_class(), \GPDFAPI::get_misc_class() ) );
	}

	/**
	 * @since 1.1
	 */
	public function tearDown() {
		remove_all_filters( 'gfpdf_show_field_value' );
		\GFAPI::delete_form( $this->form_id );
	}

	/**
	 * @since 1.1
	 */
	public function test_html() {
		$results = $this->class->html();

		/* Check all fields get rendered with an unchecked box */
		$this->assertNotFalse( strpos( $results, "&#9746;</span> First Option4 - $10.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Second Option4 - $11.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> Third Option4 - $12.00" ) );
	}

	/**
	 * @since 1.1
	 */
	public function test_html_value() {
		/* Show all values */
		add_filter( 'gfpdf_show_field_value', '__return_true' );

		$results = $this->class->html();

		$this->assertNotFalse( strpos( $results, "&#9746;</span> vFirst Option4 - $10.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> vSecond Option4 - $11.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> vThird Option4 - $12.00" ) );
	}
}
