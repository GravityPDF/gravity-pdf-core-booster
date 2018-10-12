<?php

namespace GFPDF\Tests\FieldSelector;

use GFPDF\Plugins\CoreBooster\FieldSelector\Options\FilterFields;

use WP_UnitTestCase;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2018, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF Core Booster.

    Copyright (C) 2018, Blue Liquid Designs

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
 * Class TestFilterFields
 *
 * @package GFPDF\Tests\FieldSelector
 *
 * @group   selector
 */
class TestFilterFields extends WP_UnitTestCase {

	/**
	 * @var FilterFields
	 * @since 1.1
	 */
	private $class;

	/**
	 * @since 1.1
	 */
	public function setUp() {
		$this->class = new FilterFields();
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.1
	 */
	public function test_add_filter() {
		$this->assertEquals( 10, has_filter( 'gfpdf_field_middleware', [
			$this->class,
			'filter_fields',
		] ) );
	}

	/**
	 * @since        1.1
	 * @dataProvider provider_filter_fields
	 */
	public function test_filter_fields( $expected, $field ) {
		$form_field_selector = [ 1, 4, 5, 7, 9 ];

		$this->assertSame( $expected, $this->class->filter_fields( false, $field, '', '', [ 'settings' => [ 'form_field_selector' => $form_field_selector ] ] ) );
	}

	/**
	 * @return array
	 * @since 1.1
	 */
	public function provider_filter_fields() {
		return [
			[ false, json_decode( '{"id": 1}' ) ],
			[ true, json_decode( '{"id": 2}' ) ],
			[ true, json_decode( '{"id": 3}' ) ],
			[ false, json_decode( '{"id": 4}' ) ],
			[ false, json_decode( '{"id": 5}' ) ],
			[ true, json_decode( '{"id": 6}' ) ],
			[ false, json_decode( '{"id": 7}' ) ],
			[ true, json_decode( '{"id": 8}' ) ],
			[ false, json_decode( '{"id": 9}' ) ],
		];
	}
}