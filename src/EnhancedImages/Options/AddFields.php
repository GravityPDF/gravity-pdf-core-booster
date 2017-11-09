<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedImages\Options;

use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Helper\Helper_Interface_Filters;

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
 * Class AddFields
 *
 * @package GFPDF\Plugins\CoreBooster\EnhancedImages\Options
 */
class AddFields implements Helper_Interface_Filters {

	/**
	 * @var DoesTemplateHaveGroup
	 *
	 * @since 2.0
	 */
	private $group_checker;

	/**
	 * AddFields constructor.
	 *
	 * @param DoesTemplateHaveGroup $group_checker
	 *
	 * @since 2.0
	 */
	public function __construct( DoesTemplateHaveGroup $group_checker ) {
		$this->group_checker = $group_checker;
	}


	/**
	 * Initialise our module
	 *
	 * @since 2.0
	 */
	public function init() {
		$this->add_filters();

		add_action( 'gfpdf_uploaded_images_js', function( $args ) {
			echo '<script type="text/javascript">' .
			     file_get_contents( __DIR__ . '/../Javascript/enhanced-images-settings.js' ) .
			     '</script>';
		} );
	}

	/**
	 * @since 2.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_form_settings_custom_appearance', [ $this, 'add_template_option' ], 9999 );
	}

	/**
	 * Include the field label settings for Core and Universal templates
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 2.0
	 */
	public function add_template_option( $settings ) {
		$override          = apply_filters( 'gfpdf_override_enhanced_images_fields', false, $settings ); /* Change this to true to override the core / universal check */
		$exclude_templates = apply_filters( 'gfpdf_excluded_templates_enhanced_images', [], $settings, 'product-field' ); /* Exclude this option for specific templates */

		if ( ! in_array( $this->group_checker->get_template_name(), $exclude_templates ) && ( $override || $this->group_checker->has_group() ) ) {
			$settings['display_uploaded_images'] = [
				'id'      => 'display_uploaded_images',
				'name'    => esc_html__( 'Display Uploaded Images', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes' => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'  => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'No',
				'tooltip' => '<h6>' . esc_html__( 'Display Uploaded Images', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'When enabled, uploaded images will be displayed in the PDF using the image format defined below. Non-image files will continue to be displayed as links in the standard list format.', 'gravity-pdf-core-booster' ),
			];

			$settings['display_uploaded_images_format'] = [
				'id'      => 'display_uploaded_images_format',
				'name'    => esc_html__( 'Image Format', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'1 Column' => '<img src="' . plugin_dir_url( GFPDF_CORE_BOOSTER_FILE ) . 'assets/images/image-single-column.png" width="75" alt="' . esc_html__( '1 Column', 'gravity-pdf-core-booster' ) . '" />',
					'2 Column' => '<img src="' . plugin_dir_url( GFPDF_CORE_BOOSTER_FILE ) . 'assets/images/image-two-column.png" width="75" alt="' . esc_html__( '2 Columns', 'gravity-pdf-core-booster' ) . '" />',
					'3 Column' => '<img src="' . plugin_dir_url( GFPDF_CORE_BOOSTER_FILE ) . 'assets/images/image-three-column.png" width="75" alt="' . esc_html__( '3 Columns', 'gravity-pdf-core-booster' ) . '" />',
				],
				'std'     => '1 Column',
				'class'   => 'image-radio-buttons',
				'tooltip' => '<h6>' . esc_html__( 'Image Format', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'Choose to display uploaded images in one-, two- or three-column layouts.', 'gravity-pdf-core-booster' ),
			];

			$settings['uploaded_images_max_height'] = [
				'id'    => 'uploaded_images_max_height',
				'name'  => esc_html__( 'Maximum Image Height', 'gravity-pdf-core-booster' ),
				'desc'  => esc_html__( 'Images will be constrained to the set height.', 'gravity-pdf-core-booster' ),
				'desc2' => 'px',
				'type'  => 'number',
				'size'  => 'small',
				'std'   => '300',
			];

			$settings['group_uploaded_images'] = [
				'id'      => 'group_uploaded_images',
				'name'    => esc_html__( 'Group Images?', 'gravity-pdf-core-booster' ),
				'type'    => 'radio',
				'options' => [
					'Yes' => esc_html__( 'Yes', 'gravity-pdf-core-booster' ),
					'No'  => esc_html__( 'No', 'gravity-pdf-core-booster' ),
				],
				'std'     => 'No',
				'tooltip' => '<h6>' . esc_html__( 'Group Images', 'gravity-pdf-core-booster' ) . '</h6>' . esc_html__( 'When enabled, any images in your upload fields are all grouped at the end of the PDF. This helps with the overall document readability and format.', 'gravity-pdf-core-booster' ),
			];

			$settings['uploaded_images_js'] = [
				'id'    => 'uploaded_images_js',
				'type'  => 'hook',
				'class' => 'gfpdf-hidden',
			];
		}

		return $settings;
	}
}
