<?php

namespace GFPDF\Plugins\CoreBooster\Notes\Options;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
			<div id="entry-notes">
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
			</div>
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