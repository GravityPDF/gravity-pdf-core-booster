<?php

/**
 * Plugin Name:     Gravity PDF Core Booster
 * Plugin URI:      https://gravitypdf.com/shop/core-booster-add-on/
 * Description:     Giving you greater control over the appearance of Core and Universal PDF templates for Gravity PDF.
 * Author:          Gravity PDF
 * Author URI:      https://gravitypdf.com
 * Text Domain:     gravity-pdf-core-booster
 * Domain Path:     /languages
 * Version:         0.1
 */

define( 'GFPDF_CORE_BOOSTER_FILE', __FILE__ );
define( 'GFPDF_CORE_BOOSTER_VERSION', '0.1' );

/**
 * Class GPDF_Core_Booster_Checks
 *
 * @since 1.0
 */
class GPDF_Core_Booster_Checks {

	/**
	 * Holds any blocker error messages stopping plugin running
	 *
	 * @var array
	 *
	 * @since 1.0
	 */
	private $notices = [];

	/**
	 * @var string
	 *
	 * @since 1.0
	 */
	private $required_gravitypdf_version = '4.2.0-beta1';

	/**
	 * Run our pre-checks and if it passes bootstrap the plugin
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {

		/* Test the minimum version requirements are met */
		$this->check_gravitypdf_version();

		/* Check if any errors were thrown, enqueue them and exit early */
		if ( sizeof( $this->notices ) > 0 ) {
			add_action( 'admin_notices', [ $this, 'display_notices' ] );

			return null;
		}

		require_once __DIR__ . '/src/bootstrap.php';
	}

	/**
	 * Check if the current version of Gravity PDF is compatible with this add-on
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function check_gravitypdf_version() {

		/* Check if the Gravity PDF Minimum version requirements are met */
		if ( defined( 'PDF_EXTENDED_VERSION' ) &&
		     version_compare( PDF_EXTENDED_VERSION, $this->required_gravitypdf_version, '>=' )
		) {
			return true;
		}

		/* Throw error */
		$this->notices[] = sprintf( esc_html__( 'Gravity PDF Version %s or higher is required to use this add-on. Please upgrade Gravity PDF to the latest version.', 'gravity-pdf-core-booster' ), $this->required_gravitypdf_version );
	}

	/**
	 * Helper function to easily display error messages
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function display_notices() {
		?>
        <div class="error">
            <p>
                <strong><?php esc_html_e( 'Gravity PDF Core Booster Installation Problem', 'gravity-pdf-core-booster' ); ?></strong>
            </p>

            <p><?php esc_html_e( 'The minimum requirements for the Gravity PDF Core Booster plugin have not been met. Please fix the issue(s) below to continue:', 'gravity-pdf-core-booster' ); ?></p>
            <ul style="padding-bottom: 0.5em">
				<?php foreach ( $this->notices as $notice ) : ?>
                    <li style="padding-left: 20px;list-style: inside"><?php echo $notice; ?></li>
				<?php endforeach; ?>
            </ul>
        </div>
		<?php
	}
}

/* Initialise the software */
$gravitypdf_core_booster = new GPDF_Core_Booster_Checks();
add_action( 'gfpdf_fully_loaded', [ $gravitypdf_core_booster, 'init' ] );