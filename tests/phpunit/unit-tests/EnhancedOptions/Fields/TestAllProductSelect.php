<?php

namespace GFPDF\Tests\EnhancedOptions;

use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllProduct;
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
 * Class TestAllProductSelect
 *
 * @package GFPDF\Tests\EnhancedOptions
 *
 * @group   options
 */
class TestAllProductSelect extends WP_UnitTestCase {

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
			'1'        => 'vSecond Choice1|1',
			'2'        => '',
			'3'        => '',
			'4'        => '',
			'5'        => '',
		];

		$this->class = new AllProduct( $form['fields'][0], $entry, \GPDFAPI::get_form_class(), \GPDFAPI::get_misc_class() );
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
		$this->assertNotFalse( strpos( $results, "&#9744;</span> First Choice1 - $1.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> Second Choice1 - $2.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Third Choice1 - $3.00" ) );
	}

	/**
	 * @since 1.1
	 */
	public function test_html_value() {
		/* Show all values */
		add_filter( 'gfpdf_show_field_value', '__return_true' );

		$results = $this->class->html();

		$this->assertNotFalse( strpos( $results, "&#9744;</span> vFirst Choice1 - $1.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> vSecond Choice1 - $2.00" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> vThird Choice1 - $3.00" ) );
	}
}
