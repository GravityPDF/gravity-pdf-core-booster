<?php

namespace GFPDF\Tests\EnhancedImages;

use GFPDF\Plugins\CoreBooster\EnhancedImages\Fields\ImageUploads;
use GFPDF\Plugins\CoreBooster\Shared\ImageInfo;
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
 * Class TestAllCheckbox
 *
 * @package GFPDF\Tests\EnhancedImages
 *
 * @group   images
 */
class TestImageUploads extends WP_UnitTestCase {

	/**
	 * @var ImageUploads
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$upload                = new \GF_Field_FileUpload();
		$upload->id            = 1;
		$upload->multipleFiles = true;

		$this->class = new ImageUploads( $upload, [
			'form_id' => 0,
			'id'      => 0,
			'1'       => '["http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/123-Rental-Street-Texas-Association-of-Realtors-TAR-Sellers-Disclosure-1.pdf","http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/co-blank-slate.pdf","http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/Damien-Jessie8R1.jpg","http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/IMG_9536_MODDED_R1.jpg","http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/TR4U-tax-claim-form.jpg"]',
		], \GPDFAPI::get_form_class(), \GPDFAPI::get_misc_class() );

		$this->class->set_image_helper( new ImageInfo() );
	}
	
	/**
	 * @since 1.0
	 */
	public function test_html() {
		$results = str_replace( [ "\n", "\r", '  ' ], '', $this->class->html() );

		/* Check we correctly display the images and non images */
		$this->assertNotFalse( strpos( $results, '<li id="field-1-non-image-option-0"><a href="http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/123-Rental-Street-Texas-Association-of-Realtors-TAR-Sellers-Disclosure-1.pdf">' ) );
		$this->assertNotFalse( strpos( $results, '<a href="http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/TR4U-tax-claim-form.jpg"><img src="http://local.wordpress.dev/wp-content/uploads/gravity_forms/65-8bb405e88fa978e10afa285ea463a8f7/2017/04/TR4U-tax-claim-form.jpg"/></a>' ) );
	}
}