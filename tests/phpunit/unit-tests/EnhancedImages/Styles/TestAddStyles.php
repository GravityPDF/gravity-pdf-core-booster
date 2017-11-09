<?php

namespace GFPDF\Tests\EnhancedImages;

use GFPDF\Plugins\CoreBooster\EnhancedImages\Styles\AddStyles;
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
    This file is part of Gravity PDF Core Booster.

    Copyright (C) 2017, Blue Liquid Designs

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
 * Class TestAddStyles
 *
 * @package GFPDF\Tests\EnhancedImages
 *
 * @group   images
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

		$this->assertNotFalse( strpos( $results, '.fileupload-non-image-container' ) );
	}
}