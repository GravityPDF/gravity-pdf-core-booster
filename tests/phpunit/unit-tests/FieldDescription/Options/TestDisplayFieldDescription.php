<?php

namespace GFPDF\Tests\FieldDescription;

use GFPDF\Plugins\CoreBooster\FieldDescription\Options\DisplayFieldDescription;
use WP_UnitTestCase;

use GF_Fields;

/**
 * @package     Gravity PDF Core Booster
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TestDisplayAllOptions
 *
 * @package GFPDF\Tests\EnhancedLabels
 *
 * @group   description
 */
class TestDisplayFieldLabel extends WP_UnitTestCase {

	/**
	 * @var DisplayFieldLabel
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new DisplayFieldDescription();
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_actions() {
		$this->assertEquals( 10, has_action( 'gfpdf_pre_html_fields', [
			$this->class,
			'apply_settings',
		] ) );

		$this->assertEquals( 10, has_action( 'gfpdf_post_html_fields', [
			$this->class,
			'reset_settings',
		] ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_add_field_description() {
		$field = GF_Fields::create( [
			'description' => 'My field description',
		] );

		/* Check the description is included below the value */
		$results = $this->class->add_field_description( 'Value', $field, [], [] );
		$this->assertEquals( 'Value', substr( $results, 0, 5 ) );
		$this->assertNotFalse( strpos( $results, 'My field description' ) );

		/* Check the description is included above the value */
		$results = $this->class->add_field_description( 'Value', $field, [], [
			'labelPlacement'       => 'top_label',
			'descriptionPlacement' => 'above',
		] );

		$this->assertNotEquals( 'Value', substr( $results, 0, 5 ) );
		$this->assertNotFalse( strpos( $results, 'My field description' ) );
	}

	/**
	 * @since 1.0
	 */
	public function test_reset_settings() {
		$this->class->apply_settings( '', [ 'settings' => [ 'include_field_description' => 'Yes' ] ] );
		$this->assertEquals( 10, has_action( 'gfpdf_pdf_field_content', [ $this->class, 'add_field_description' ] ) );

		$this->class->reset_settings();
		$this->assertFalse( has_action( 'gfpdf_pdf_field_content', [ $this->class, 'add_field_description' ] ) );
	}
}