<?php

namespace GFPDF\Tests\FieldSelector;

use GFPDF\Plugins\CoreBooster\FieldSelector\Options\FilterFields;

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
 * Class TestFilterFields
 *
 * @package GFPDF\Tests\FieldSelector
 *
 * @group   selector
 */
class TestFilterFields extends WP_UnitTestCase {

	/**
	 * @var FilterFields
	 * @since 1.1
	 */
	private $class;

	/**
	 * @since 1.1
	 */
	public function setUp() {
		$this->class = new FilterFields();
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.1
	 */
	public function test_add_filter() {
		$this->assertEquals( 10, has_filter( 'gfpdf_current_pdf_configuration', [
			$this->class,
			'disable_excluded_fields',
		] ) );

		$this->assertEquals( 20, has_filter( 'gfpdf_field_middleware', [
			$this->class,
			'filter_fields',
		] ) );
	}

	/**
	 * @since 1.1
	 */
	public function test_disable_excluded_fields() {
		$results = $this->class->disable_excluded_fields( [ 'settings' => [], 'meta' => [ 'exclude' => true ] ] );
		$this->assertTrue( $results['meta']['exclude'] );

		$results = $this->class->disable_excluded_fields( [ 'settings' => [ 'form_field_selector' => '' ], 'meta' => [ 'exclude' => true ] ] );
		$this->assertFalse( $results['meta']['exclude'] );
	}

	/**
	 * @since        1.1
	 * @dataProvider provider_filter_fields
	 */
	public function test_filter_fields( $expected, $field ) {
		$form_field_selector = [ 1, 4, 5, 7, 9 ];

		$this->assertSame( $expected, $this->class->filter_fields( false, $field, '', '', [ 'settings' => [ 'form_field_selector' => $form_field_selector ] ] ) );
	}

	/**
	 * @return array
	 * @since 1.1
	 */
	public function provider_filter_fields() {
		return [
			[ false, json_decode( '{"id": 1}' ) ],
			[ true, json_decode( '{"id": 2}' ) ],
			[ true, json_decode( '{"id": 3}' ) ],
			[ false, json_decode( '{"id": 4}' ) ],
			[ false, json_decode( '{"id": 5}' ) ],
			[ true, json_decode( '{"id": 6}' ) ],
			[ false, json_decode( '{"id": 7}' ) ],
			[ true, json_decode( '{"id": 8}' ) ],
			[ false, json_decode( '{"id": 9}' ) ],
		];
	}
}