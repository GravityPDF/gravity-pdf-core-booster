<?php

namespace GFPDF\Plugins\CoreBooster\EnhancedImages\Fields;

use GFPDF\Plugins\CoreBooster\Shared\ImageInfo;
use GFPDF\Helper\Fields\Field_Post_Image;
use GFPDF\Helper\Helper_Abstract_Fields;

/**
 * Gravity Forms Field
 *
 * @package     Gravity PDF
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
 * Controls the display and output of the Checkbox HTML
 *
 * @since 2.0
 */
class PostImageUploads extends Field_Post_Image {

	/**
	 * @var ImageInfo
	 *
	 * @since 2.0
	 */
	protected $image_info;

	/**
	 * @var array
	 *
	 * @since 2.0
	 */
	protected $pdf_settings;

	/**
	 * @param ImageInfo $image_info
	 *
	 * @since 2.0
	 */
	public function set_image_helper( ImageInfo $image_info ) {
		$this->image_info = $image_info;
	}

	/**
	 * @param $settings
	 *
	 * @since 2.0
	 */
	public function set_pdf_settings( $settings ) {
		$this->pdf_settings = $settings;
	}

	/**
	 * Include all checkbox options in the list and tick the ones that were selected
	 *
	 * @param string $value
	 * @param bool   $label
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function html( $value = '', $label = true ) {
		$image = $this->value();
		$html  = '';

		$should_group_images = ( isset( $this->pdf_settings['group_uploaded_images'] ) ) ? $this->pdf_settings['group_uploaded_images'] : 'No';
		if ( isset( $image['path'] ) && is_file( $image['path'] ) && $should_group_images === 'No' ) {
			$html .= $this->get_image_html( $image );
		}

		return Helper_Abstract_Fields::html( $html );
	}

	/**
	 * @return bool
	 *
	 * @since 2.0
	 */
	public function is_empty() {
		$should_group_images = ( isset( $this->pdf_settings['group_uploaded_images'] ) ) ? $this->pdf_settings['group_uploaded_images'] : 'No';

		if ( $should_group_images === 'Yes' ) {
			return true;
		}

		return parent::is_empty();
	}

	/**
	 * @return bool
	 *
	 * @since 2.0
	 */
	public function has_images() {
		$image = $this->value();

		return isset( $image['path'] ) && is_file( $image['path'] );
	}

	/**
	 * @return string
	 *
	 * @since 2.0
	 */
	public function group_html() {
		$image = $this->value();
		$html  = $this->get_image_html( $image );

		return Helper_Abstract_Fields::html( $html );
	}

	/**
	 * @param $image
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_image_html( $image ) {
		$max_image_height = ( isset( $this->pdf_settings['uploaded_images_max_height'] ) ) ? $this->pdf_settings['uploaded_images_max_height'] : '300';
		$resized_image    = $this->image_info->get_image_resized_filepath( $image['path'] );
		$img_string       = ( is_file( $resized_image ) ) ? $resized_image : $image['path'];

		ob_start();
		?>
        <div class="fileupload-images-container <?php echo $this->get_image_column_class(); ?>">
            <div id="field-<?php echo $this->field->id; ?>-post-image"
                 class="fileupload-images">
                <a href="<?php echo esc_url( $image['url'] ); ?>">
                    <img src="<?php echo $img_string; ?>" style="max-height: <?php echo $max_image_height; ?>px" />

					<?php if ( ! empty( $image['title'] ) ): ?>
                        <div class="gfpdf-post-image-title"><?php echo $image['title']; ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $image['caption'] ) ): ?>
                        <div class="gfpdf-post-image-caption"><?php echo $image['caption']; ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $image['description'] ) ): ?>
                        <div class="gfpdf-post-image-description"><?php echo $image['description']; ?></div>
					<?php endif; ?>
                </a>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * @return string
	 *
	 * @since 2.0
	 */
	protected function get_image_column_class() {
		/* Determine how the images should be displayed */
		$img_format = ( isset( $this->pdf_settings['display_uploaded_images_format'] ) ) ? $this->pdf_settings['display_uploaded_images_format'] : '1 Column';
		switch ( $img_format ) {
			case '2 Column':
				$img_format_css = 'fileupload-images-two-col';
			break;

			case '3 Column':
				$img_format_css = 'fileupload-images-three-col';
			break;

			default:
				$img_format_css = 'fileupload-images-one-col';
			break;
		}

		return $img_format_css;
	}
}