<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedImages\ImageManipulation;

use GFPDF\Plugins\CoreBooster\Shared\ImageInfo;
use GFPDF\Helper\Helper_Interface_Filters;
use abeautifulsite\SimpleImage;

use Exception;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2017, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
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
 * Class Resize
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedImages\ImageManipulation
 */
class Resize implements Helper_Interface_Filters {

	/**
	 * @var ImageInfo
	 *
	 * @since 2.0
	 */
	protected $image_info;

	/**
	 * Resize constructor.
	 *
	 * @param ImageInfo $image_info
	 *
	 * @since 2.0
	 */
	public function __construct( ImageInfo $image_info ) {
		$this->image_info = $image_info;
	}

	/**
	 * Initialise our module
	 *
	 * @since 2.0
	 */
	public function init() {
		$this->add_filters();
	}

	public function add_filters() {
		add_filter( 'gfpdf_queue_initialise', [ $this, 'queue_image_resize' ], 10, 3 );
	}

	public function queue_image_resize( $queue_data, $entry, $form ) {
		$files = $this->maybe_resize_images( $entry, $form );

		foreach ( $files as $file ) {
			$queue_data[] = [
				'id'   => 'image-resize-' . $this->image_info->get_image_name( $file ),
				'func' => [ $this, 'handle_image_resize' ],
				'args' => [ $file ],
			];
		}

		return $queue_data;
	}

	/**
	 * @param $entry
	 * @param $form
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function maybe_resize_images( $entry, $form ) {

		$files_to_resize = [];

		/* Get all file upload fields in the form */
		$upload_fields = array_filter( $form['fields'], function( $field ) {
			return $field->get_input_type() === 'fileupload' || $field->get_input_type() === 'post_image';
		} );

		/* Resize our images (if any) */
		foreach ( $upload_fields as $field ) {
			$files_to_resize = array_merge( $files_to_resize, $this->get_upload_files( $field, $entry ) );
		}

		return $files_to_resize;
	}

	/**
	 * Resize image, if not already resized
	 *
	 * @param string $path
	 *
	 * @since 2.0
	 */
	public function handle_image_resize( $path ) {
		if ( ! is_file( $this->image_info->get_image_resized_filepath( $path ) ) ) {
			$this->resize_image( $path );
		}
	}

	/**
	 * @param string $path
	 *
	 * @since 2.0
	 */
	public function resize_image( $path ) {
		$resize_image_path = $this->image_info->get_image_resized_filepath( $path );

		if ( ! is_file( $path ) ) {
			return;
		}

		try {
			$img = new SimpleImage( $path );
			$img->best_fit( 1000, 1000 )
			    ->auto_orient()
			    ->save( $resize_image_path );

			unset( $img );
		} catch ( Exception $e ) {
			/* Log error */
		}
	}

	/**
	 * @param $field
	 * @param $entry
	 *
	 * @return array
	 *
	 * @since 2.0
	 */
	protected function get_upload_files( $field, $entry ) {
		$files = $entry[ $field->id ];
		$files = ( $field->multipleFiles ) ? (array) json_decode( $files ) : [ $files ];

		/* If Post Image, force into the correct format for image processing */
		if ( $field->get_input_type() === 'post_image' ) {
			$image_data = explode( '|:|', $files[0] );
			$files[0] = $image_data[0];
		}

		/* Convert Urls to local paths */
		$paths = array_map( function( $file ) {
			return $this->image_info->get_file_path( $file );
		}, $files );

		/* Filter out non-images */
		return array_filter( $paths, function( $path ) {
			return $this->image_info->does_file_have_image_extension( $path );
		} );
	}
}