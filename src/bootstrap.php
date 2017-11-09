<?php

namespace GFPDF\Plugins\CoreBooster;

use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;
use GFPDF\Plugins\CoreBooster\Shared\ImageInfo;
use GFPDF\Plugins\CoreBooster\EnhancedLabels\Options\AddFields as LabelsAddFields;
use GFPDF\Plugins\CoreBooster\EnhancedLabels\Options\DisplayFieldLabel;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Options\AddFields as OptionsAddFields;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Options\DisplayAllOptions;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Options\DisplayLabelOrValue;
use GFPDF\Plugins\CoreBooster\EnhancedOptions\Styles\AddStyles as OptionsAddStyles;
use GFPDF\Plugins\CoreBooster\FieldDescription\Options\AddFields as FieldDescriptionAddFields;
use GFPDF\Plugins\CoreBooster\FieldDescription\Options\DisplayFieldDescription;
use GFPDF\Plugins\CoreBooster\ProductTable\Options\AddFields as ProductTableAddFields;
use GFPDF\Plugins\CoreBooster\ProductTable\Options\DisableProductTable;
use GFPDF\Plugins\CoreBooster\EnhancedImages\Options\AddFields as EnhancedImagesAddFields;
use GFPDF\Plugins\CoreBooster\EnhancedImages\ImageManipulation\Resize;
use GFPDF\Plugins\CoreBooster\EnhancedImages\Options\DisplayImages;
use GFPDF\Plugins\CoreBooster\EnhancedImages\Styles\AddStyles as ImagesAddStyles;

use GFPDF\Helper\Licensing\EDD_SL_Plugin_Updater;
use GFPDF\Helper\Helper_Abstract_Addon;
use GFPDF\Helper\Helper_Singleton;
use GFPDF\Helper\Helper_Logger;
use GFPDF\Helper\Helper_Notices;

use GPDFAPI;

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

/* Load Composer */
require_once( __DIR__ . '/../vendor/autoload.php' );

/**
 * Class Bootstrap
 *
 * @package GFPDF\Plugins\CoreBooster
 */
class Bootstrap extends Helper_Abstract_Addon {

	/**
	 * Initialise the plugin classes and pass them to our parent class to
	 * handle the rest of the bootstrapping (licensing ect)
	 *
	 * @param array $classes An array of classes to store in our singleton
	 *
	 * @since 1.0
	 */
	public function init( $classes = [] ) {
		/* Create new intances of the plugin's classes */
		$group_checker = new DoesTemplateHaveGroup( GPDFAPI::get_mvc_class( 'Model_Form_Settings' ), GPDFAPI::get_templates_class(), $this->log );

		/* Register our classes and pass back up to the parent initialiser */
		$classes = array_merge( $classes, [
			new LabelsAddFields( $group_checker, $this->log ),
			new DisplayFieldLabel( $this->log ),
			new OptionsAddFields( $group_checker, $this->log ),
			new DisplayAllOptions( $this->log ),
			new DisplayLabelOrValue( $this->log ),
			new OptionsAddStyles( $this->log ),
			new FieldDescriptionAddFields( $group_checker, $this->log ),
			new DisplayFieldDescription( $this->log ),
			new ProductTableAddFields( $group_checker, $this->log ),
			new DisableProductTable( $this->log ),
			new EnhancedImagesAddFields( $group_checker ),
			new Resize( new ImageInfo() ),
			new DisplayImages( new ImageInfo() ),
			new ImagesAddStyles()
		] );

		/* Run the setup */
		parent::init( $classes );
	}

	/**
	 * Check the plugin's license is active and initialise the EDD Updater
	 *
	 * @since 1.0
	 */
	public function plugin_updater() {

		$license_info = $this->get_license_info();

		new EDD_SL_Plugin_Updater(
			$this->data->store_url,
			$this->get_main_plugin_file(),
			[
				'version'   => $this->get_version(),
				'license'   => $license_info['license'],
				'item_name' => $this->get_short_name(),
				'author'    => $this->get_author(),
				'beta'      => false,
			]
		);

		$this->log->notice( sprintf( '%s plugin updater initialised', $this->get_name() ) );
	}
}

/* Use the filter below to replace and extend our Bootstrap class if needed */
$name = 'Gravity PDF Core Booster';
$slug = 'gravity-pdf-core-booster';

$plugin = apply_filters( 'gfpdf_core_booster_initialise', new Bootstrap(
	$slug,
	$name,
	'Gravity PDF',
	GFPDF_CORE_BOOSTER_VERSION,
	GFPDF_CORE_BOOSTER_FILE,
	GPDFAPI::get_data_class(),
	GPDFAPI::get_options_class(),
	new Helper_Singleton(),
	new Helper_Logger( $slug, $name ),
	new Helper_Notices()
) );

$plugin->set_edd_download_id( '13035' );
$plugin->set_addon_documentation_slug( 'shop-plugin-core-booster-add-on' );
$plugin->init();

/* Use the action below to access our Bootstrap class, and any singletons saved in $plugin->singleton */
do_action( 'gfpdf_core_booster_bootrapped', $plugin );