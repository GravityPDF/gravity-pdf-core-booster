<?php

namespace GFPDF\Tests;

use GFPDF\Plugins\CoreBooster\Options\DisplayLabelOrValue;
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
 * Class TestDisplayLabelOrValue
 *
 * @package GFPDF\Tests
 *
 * @group   display-label-value
 */
class TestDisplayLabelOrValue extends WP_UnitTestCase {

	/**
	 * @var DisplayLabelOrValue
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new DisplayLabelOrValue();
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_actions() {
		$this->assertEquals( 10, has_action( 'gfpdf_pre_html_fields', [
			$this->class,
			'get_settings',
		] ) );

		$this->assertEquals( 10, has_action( 'gfpdf_post_html_fields', [
			$this->class,
			'reset_settings',
		] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_get_settings() {

		/* Stub our settings */
		$settings = [
			'settings' => [
				'option_label_or_value' => 'Value',
			],
		];

		/* Check the filter doesn't exist when our option isn't set */
		$this->class->get_settings( [], [ 'settings' => [] ] );
		$this->assertFalse( has_filter( 'gfpdf_show_field_value', [ $this->class, 'show_option_value' ] ) );

		/* Check our filter exist when our option is set */
		$this->class->get_settings( [], $settings );
		$this->assertEquals( 10, has_filter( 'gfpdf_show_field_value', [ $this->class, 'show_option_value' ] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_show_option_value() {
		$this->assertTrue( $this->class->show_option_value() );
	}
}