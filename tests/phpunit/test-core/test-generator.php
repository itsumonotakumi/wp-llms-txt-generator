<?php
/**
 * Class GeneratorTest
 *
 * @package WP_LLMS_TXT_Generator
 */

use ItsumonoTakumi\WpLlmsTxtGenerator\Core\Generator;

/**
 * Generator test case.
 */
class GeneratorTest extends WP_UnitTestCase {

	/**
	 * Test instance creation
	 */
	public function test_constructor() {
		$generator = new Generator();
		
		// Verify that the necessary hooks are added
		$this->assertEquals( 10, has_action( 'admin_menu', array( $generator, 'add_admin_menu' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $generator, 'load_textdomain' ) ) );
		$this->assertEquals( 10, has_action( 'admin_init', array( $generator, 'register_settings' ) ) );
		$this->assertEquals( 10, has_action( 'admin_post_generate_llms_txt', array( $generator, 'handle_generate_llms_txt' ) ) );
		$this->assertEquals( 10, has_action( 'init', array( $generator, 'handle_view_llms_txt_files' ) ) );
	}

	/**
	 * Test admin menu setup
	 */
	public function test_add_admin_menu() {
		global $admin_page_hooks;
		
		$generator = new Generator();
		$generator->add_admin_menu();
		
		// Check if the menu item is registered
		$this->assertArrayHasKey( 'options-general.php', $admin_page_hooks );
	}

	/**
	 * Test settings registration
	 */
	public function test_register_settings() {
		global $wp_registered_settings;
		
		$generator = new Generator();
		$generator->register_settings();
		
		// Check if settings are registered
		$this->assertArrayHasKey( 'llms_txt_generator_post_types', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_custom_header', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_include_excerpt', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_auto_update', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_debug_mode', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_schedule_enabled', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_schedule_frequency', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_include_urls', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_exclude_urls', $wp_registered_settings );
		$this->assertArrayHasKey( 'llms_txt_generator_keep_settings', $wp_registered_settings );
	}
}
