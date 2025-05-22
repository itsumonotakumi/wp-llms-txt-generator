<?php
/**
 * Admin Page class for WP LLMS TXT Generator
 *
 * @package ItsumonoTakumi\WpLlmsTxtGenerator
 * @since 1.0.0
 */

namespace ItsumonoTakumi\WpLlmsTxtGenerator\Admin;

/**
 * Admin Page class for WP LLMS TXT Generator
 *
 * This class handles the admin page rendering and functionality.
 *
 * @since 1.0.0
 */
class Page {
    /**
     * Render the admin page content
     *
     * Creates a tabbed interface with settings, generation, and help tabs.
     *
     * @since 1.0.0
     * @return void
     */
    public function render() {
        // Include the original admin page content function
        require_once plugin_dir_path(WP_LLMS_TXT_GENERATOR_FILE) . 'admin-page.php';
        
        // Call the original function
        llms_txt_generator_admin_page_content();
    }
}
