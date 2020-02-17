<?php

namespace GFPDF\Tests\EnhancedOptions;

use GFPDF\Plugins\CoreBooster\EnhancedOptions\Fields\AllSelect;
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
 * Class TestAllSelect
 *
 * @package GFPDF\Tests\EnhancedOptions
 *
 * @group   options
 */
class TestAllSelect extends WP_UnitTestCase {

	/**
	 * @var AllCheckbox
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$select          = new \GF_Field_Select();
		$select->id      = 1;
		$select->choices = [
			[
				'text'  => 'Option 1',
				'value' => 'Option 1 Value',
			],

			[
				'text'  => 'Option 2',
				'value' => 'Option 2 Value',
			],

			[
				'text'  => 'Option 3',
				'value' => 'Option 3 Value',
			],

			[
				'text'  => 'Option 4',
				'value' => 'Option 4 Value',
			],
		];

		$this->class = new AllSelect( $select, [
			'form_id' => 0,
			'id'      => 0,
			'1'     => 'Option 2 Value',
		], \GPDFAPI::get_form_class(), \GPDFAPI::get_misc_class() );
	}

	/**
	 * @since 1.0
	 */
	public function tearDown() {
		remove_all_filters( 'gfpdf_show_field_value' );
	}

	/**
	 * @since 1.0
	 */
	public function test_html() {
		$results = $this->class->html();

		/* Check all fields get rendered with an unchecked box */
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 1" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> Option 2" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 3" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 4" ) );

		/* Show all values */
		add_filter( 'gfpdf_show_field_value', '__return_true' );

		$results = $this->class->html();

		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 1 Value" ) );
		$this->assertNotFalse( strpos( $results, "&#9746;</span> Option 2 Value" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 3 Value" ) );
		$this->assertNotFalse( strpos( $results, "&#9744;</span> Option 4 Value" ) );
	}
}