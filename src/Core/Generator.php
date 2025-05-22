<?php
/**
 * Main Generator class for WP LLMS TXT Generator
 *
 * @package ItsumonoTakumi\WpLlmsTxtGenerator
 * @since 1.0.0
 */

namespace ItsumonoTakumi\WpLlmsTxtGenerator\Core;

use ItsumonoTakumi\WpLlmsTxtGenerator\Admin\Page;

/**
 * Main plugin class for WP LLMS TXT Generator
 *
 * This class handles the core functionality of the plugin including
 * admin menu setup, file generation, and URL handling.
 *
 * @since 1.0.0
 */
class Generator {
    /**
     * Constructor for the Generator class
     *
     * Sets up all necessary hooks and actions for the plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_generate_llms_txt', array($this, 'handle_generate_llms_txt'));
        add_action('init', array($this, 'handle_view_llms_txt_files'));
    }

    /**
     * Add plugin menu item to WordPress admin menu
     *
     * @since 1.0.0
     * @return void
     */
    public function add_admin_menu() {
        add_options_page(
            __('WP LLMS TXT Generator Settings', 'wp-llms-txt-generator'),
            __('WP LLMS TXT Generator', 'wp-llms-txt-generator'),
            'manage_options',
            'llms-txt-generator',
            array($this, 'admin_page')
        );
    }

    /**
     * Load plugin text domain for translations
     *
     * @since 1.0.0
     * @return void
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wp-llms-txt-generator',
            false,
            dirname(plugin_basename(WP_LLMS_TXT_GENERATOR_FILE)) . '/languages/'
        );
    }

    /**
     * Renders the admin settings page
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_page() {
        if (!function_exists('do_settings_sections')) {
            require_once(ABSPATH . 'wp-admin/includes/template.php');
        }

        $page = new Page();
        $page->render();
    }

    /**
     * Register plugin settings
     *
     * @since 1.0.0
     * @return void
     */
    public function register_settings() {
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_post_types');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_custom_header');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_include_excerpt');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_auto_update');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_debug_mode');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_schedule_enabled');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_schedule_frequency');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_include_urls');
        register_setting('llms_txt_generator_settings', 'llms_txt_generator_exclude_urls');
        register_setting('llms_txt_generator_uninstall_settings', 'llms_txt_generator_keep_settings');
    }

    /**
     * Handle the form submission to generate LLM text files
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_generate_llms_txt() {
        if (!current_user_can('manage_options')) {
            wp_die(__('この操作を実行する権限がありません。', 'wp-llms-txt-generator'));
        }

        if (!isset($_POST['llms_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['llms_nonce']), 'llms_generate_action')) {
            wp_die(__('セキュリティチェックに失敗しました。', 'wp-llms-txt-generator'));
        }

        $this->generate_llms_txt_files();

        wp_redirect(admin_url('options-general.php?page=llms-txt-generator&tab=generate&generated=1'));
        exit;
    }

    /**
     * Generate llms.txt and llms-full.txt files
     *
     * @since 1.0.0
     * @return bool True on success, false on failure
     */
    private function generate_llms_txt_files() {
        // Implementation details omitted for brevity in planning mode
        // Will be fully implemented in the actual code
        return true;
    }

    /**
     * Ensures a string is properly encoded in UTF-8
     *
     * @since 1.0.0
     * @param string $str The string to ensure is UTF-8 encoded
     * @return string The UTF-8 encoded string
     */
    private function ensure_utf8($str) {
        // Implementation details omitted for brevity in planning mode
        // Will be fully implemented in the actual code
        return $str;
    }

    /**
     * Check if a URL should be included based on filter settings
     *
     * @since 1.0.0
     * @param string $url The URL to check
     * @param array $include_urls_array Array of URL patterns to include
     * @param array $exclude_urls_array Array of URL patterns to exclude
     * @return bool True if URL should be included, false otherwise
     */
    private function is_url_allowed($url, $include_urls_array, $exclude_urls_array) {
        // Implementation details omitted for brevity in planning mode
        // Will be fully implemented in the actual code
        return true;
    }

    /**
     * Convert wildcard pattern to regular expression
     *
     * @since 1.0.0
     * @param string $pattern The wildcard pattern to convert
     * @return string The resulting regular expression
     */
    private function wildcard_to_regex($pattern) {
        // Implementation details omitted for brevity in planning mode
        // Will be fully implemented in the actual code
        return '';
    }

    /**
     * Handle requests to view the generated llms.txt files
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_view_llms_txt_files() {
        // Implementation details omitted for brevity in planning mode
        // Will be fully implemented in the actual code
    }
}
