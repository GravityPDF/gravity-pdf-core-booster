<?php

namespace GFPDF\Tests;

use GFPDF\Plugins\CoreBooster\Fields\AllMultiselect;
use WP_UnitTestCase;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2017, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF.

    Gravity PDF – Copyright (C) 2016, Blue Liquid Designs

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
 * Class TestAllMultiselect
 *
 * @package GFPDF\Tests
 *
 * @group   fields
 */
class TestAllMultiselect extends WP_UnitTestCase {

	/**
	 * @var AllCheckbox
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$multiselect          = new \GF_Field_MultiSelect();
		$multiselect->id      = 1;
		$multiselect->choices = [
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

		$multiselect->inputs = [
			[
				'id'    => '1.1',
				'label' => 'Option 1',
			],

			[
				'id'    => '1.2',
				'label' => 'Option 2',
			],

			[
				'id'    => '1.3',
				'label' => 'Option 3',
			],

			[
				'id'    => '1.4',
				'label' => 'Option 4',
			],
		];

		$this->class = new AllMultiselect( $multiselect, [
			'form_id' => 0,
			'id'      => 0,
			'1.2'     => 'Option 2 Value',
			'1.4'     => 'Option 4 Value',
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
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9744;</span> Option 1" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9746;</span> Option 2" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9744;</span> Option 3" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9746;</span> Option 4" ) );

		/* Show all values */
		add_filter( 'gfpdf_show_field_value', '__return_true' );

		$results = $this->class->html();

		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9744;</span> Option 1 Value" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9746;</span> Option 2 Value" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9744;</span> Option 3 Value" ) );
		$this->assertNotFalse( strpos( $results, "<span style='font-size: 125%;'>&#9746;</span> Option 4 Value" ) );
	}
}