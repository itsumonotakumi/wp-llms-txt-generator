<?php
/**
 * Class PageTest
 *
 * @package WP_LLMS_TXT_Generator
 */

use ItsumonoTakumi\WpLlmsTxtGenerator\Admin\Page;

/**
 * Page test case.
 */
class PageTest extends WP_UnitTestCase {

	/**
	 * Test instance creation
	 */
	public function test_constructor() {
		$page = new Page();
		$this->assertInstanceOf( Page::class, $page );
	}

	/**
	 * Test render method
	 */
	public function test_render() {
		// This test requires output buffering to capture the rendered content
		ob_start();
		$page = new Page();
		$page->render();
		$output = ob_get_clean();
		
		// Check if the output contains expected content
		$this->assertNotEmpty( $output );
	}
}
