<?php

namespace GFPDF\Tests\EnhancedImages;

use GFPDF\Plugins\CoreBooster\EnhancedImages\Options\AddFields;
use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;

use GPDFAPI;
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
 * Class TestAddFields
 *
 * @package GFPDF\Tests\EnhancedImages
 *
 * @group   images
 */
class TestAddFields extends WP_UnitTestCase {

	/**
	 * @var AddFields
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {

		/* Setup our class mocks */
		$form_settings = $this->getMock(
			'\GFPDF\Model\Model_Form_Settings',
			[ 'get_template_name_from_current_page' ],
			[
				GPDFAPI::get_form_class(),
				GPDFAPI::get_log_class(),
				GPDFAPI::get_data_class(),
				GPDFAPI::get_options_class(),
				GPDFAPI::get_misc_class(),
				GPDFAPI::get_notice_class(),
				GPDFAPI::get_templates_class(),
			]
		);

		$form_settings->method( 'get_template_name_from_current_page' )
		              ->will( $this->onConsecutiveCalls( 'zadani', 'zadani', 'sabre', 'sabre', 'other', 'other' ) );

		$template = $this->getMock(
			'\GFPDF\Helper\Helper_Templates',
			[ 'get_template_info_by_id' ],
			[ GPDFAPI::get_log_class(), GPDFAPI::get_data_class() ]
		);

		$template->method( 'get_template_info_by_id' )
		         ->will(
			         $this->returnValueMap( [
					         [ 'zadani', [ 'group' => 'Core' ] ],
					         [ 'sabre', [ 'group' => 'Universal (Premium)' ] ],
					         [ 'other', [ 'group' => 'Legacy' ] ],
				         ]
			         )
		         );

		$this->class = new AddFields( new DoesTemplateHaveGroup( $form_settings, $template ) );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_filter() {
		$this->assertEquals( 9999, has_filter( 'gfpdf_form_settings_custom_appearance', [
			$this->class,
			'add_template_option',
		] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_add_template_option() {

		/* Check our two options are included */
		$results = $this->class->add_template_option( [] );
		$this->assertCount( 1, $results );
		$this->assertArrayHasKey( 'display_uploaded_images', $results );

		/* Check our two options are included when using a universal template */
		$this->assertCount( 1, $this->class->add_template_option( [] ) );

		/* Check our two options are not included when using a non-core or universal template */
		$this->assertCount( 0, $this->class->add_template_option( [] ) );

		/* Check our option is included when we use our overriding filter */
		add_filter( 'gfpdf_override_enhanced_images_fields', '__return_true' );
		$this->assertCount( 1, $this->class->add_template_option( [] ) );
	}
}