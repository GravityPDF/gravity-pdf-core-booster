<?php

namespace GFPDF\Plugins\CoreBooster\Notes\Options;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;

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
 * Class DisplayNotes
 *
 * @package GFPDF\Plugins\CoreBooster\Notes\Options
 */
class DisplayNotes implements Helper_Interface_Actions {

	/**
	 * @since 1.1
	 */
	use Helper_Trait_Logger;

	/**
	 * Initialise our module
	 *
	 * @since 1.1
	 */
	public function init() {
		$this->add_actions();
	}

	/**
	 * @since 1.1
	 */
	public function add_actions() {
		add_action( 'gfpdf_pre_html_fields', [ $this, 'apply_settings' ], 10, 2 );
		add_action( 'gfpdf_post_html_fields', [ $this, 'reset_settings' ], 25 );
	}

	/**
	 * Apply our filter to show the option value if our saved setting value is correct
	 *
	 * @param array $entry
	 * @param array $settings
	 *
	 * @since 1.1
	 */
	public function apply_settings( $entry, $settings ) {
		$settings = $settings['settings'];

		if ( isset( $settings['display_entry_notes'] ) && $settings['display_entry_notes'] === 'Yes' ) {
			$this->logger->notice( 'Show Entry Notes in PDF' );

			add_action( 'gfpdf_post_html_fields', [ $this, 'add_entry_notes' ], 20, 2 );
		}
	}

	/**
	 *
	 * @param array $entry
	 *
	 * @since 1.1
	 */
	public function add_entry_notes( $entry ) {
		$notes = \GFFormsModel::get_lead_notes( $entry['id'] );

		if ( count( $notes ) > 0 ): ?>
			<h3><?= esc_html__( 'Notes', 'gravityforms' ) ?></h3>
			<?php foreach ( $notes as $note ): ?>
				<div class="note">
					<div class="note-avatar">
						<img src="<?= get_avatar_url( $note->user_id, [ 'size' => ( 48 * 2 ) ] ) ?>" />
					</div>

					<div class="note-meta">
						<div class="note-label"><strong><?= esc_html( $note->user_name ) ?></strong></div>

						<div class="note-additional-info">
							<?= esc_html( $note->user_email ) ?><br>
							<em>
								<?= esc_html__( 'added on', 'gravityforms' ); ?>
								<?= esc_html( \GFCommon::format_date( $note->date_created, false ) ) ?>
							</em>
						</div>
					</div>

					<div class="note-value">
						<?= nl2br( esc_html( $note->value ) ) ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif;
	}

	/**
	 * Remove the filter we added
	 *
	 * @since 1.1
	 */
	public function reset_settings() {
		remove_filter( 'gfpdf_show_field_value', [ $this, 'maybe_show_field_value' ] );
		remove_action( 'gfpdf_post_html_fields', [ $this, 'add_entry_notes' ] );
	}
}