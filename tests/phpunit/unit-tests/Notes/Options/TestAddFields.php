<?php

namespace GFPDF\Tests\Notes;

use GFPDF\Plugins\CoreBooster\Notes\Options\AddFields;
use GFPDF\Plugins\CoreBooster\Shared\DoesTemplateHaveGroup;

use GPDFAPI;

use WP_UnitTestCase;

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
 * Class TestAddFields
 *
 * @package GFPDF\Tests\EnhancedOptions
 *
 * @group   notes
 */
class TestAddFields extends WP_UnitTestCase {

	/**
	 * @var AddFields
	 * @since 1.1
	 */
	private $class;

	/**
	 * @since 1.1
	 */
	public function setUp() {

		/* Setup our class mocks */
		$form_settings = $this->getMockBuilder( '\GFPDF\Model\Model_Form_Settings' )
		                      ->setConstructorArgs( [
			                      GPDFAPI::get_form_class(),
			                      GPDFAPI::get_log_class(),
			                      GPDFAPI::get_data_class(),
			                      GPDFAPI::get_options_class(),
			                      GPDFAPI::get_misc_class(),
			                      GPDFAPI::get_notice_class(),
			                      GPDFAPI::get_templates_class(),
		                      ] )
		                      ->setMethods( [ 'get_template_name_from_current_page' ] )
		                      ->getMock();

		$form_settings->method( 'get_template_name_from_current_page' )
		              ->will( $this->onConsecutiveCalls( 'zadani', 'zadani', 'sabre', 'sabre', 'other', 'other' ) );

		$template = $this->getMockBuilder( '\GFPDF\Helper\Helper_Templates' )
		                 ->setConstructorArgs( [
			                 GPDFAPI::get_log_class(),
			                 GPDFAPI::get_data_class(),
			                 GPDFAPI::get_form_class(),
		                 ] )
		                 ->setMethods( [ 'get_template_info_by_id' ] )
		                 ->getMock();

		$template->method( 'get_template_info_by_id' )
		         ->will(
			         $this->returnValueMap( [
					         [ 'zadani', [ 'group' => 'Core' ] ],
					         [ 'sabre', [ 'group' => 'Universal (Premium)' ] ],
					         [ 'other', [ 'group' => 'Legacy' ] ],
				         ]
			         )
		         );

		$template = new DoesTemplateHaveGroup( $form_settings, $template );
		$template->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class = new AddFields( $template );
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.1
	 */
	public function test_add_filter() {
		$this->assertEquals( 9999, has_filter( 'gfpdf_form_settings_custom_appearance', [
			$this->class,
			'add_template_option',
		] ) );
	}

	/**
	 * @since 1.1
	 */
	public function test_add_template_option() {

		/* Check our two options are included */
		$results = $this->class->add_template_option( [] );
		$this->assertCount( 1, $results );
		$this->assertArrayHasKey( 'display_entry_notes', $results );

		/* Check our two options are included when using a universal template */
		$this->assertCount( 1, $this->class->add_template_option( [] ) );

		/* Check our two options are not included when using a non-core or universal template */
		$this->assertCount( 0, $this->class->add_template_option( [] ) );

		/* Check our option is included when we use our overriding filter */
		add_filter( 'gfpdf_override_notes_fields', '__return_true' );
		$this->assertCount( 1, $this->class->add_template_option( [] ) );
	}
}