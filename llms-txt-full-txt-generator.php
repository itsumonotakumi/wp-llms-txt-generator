
<?php
/**
 * Plugin Name: LLMS TXT and Full TXT Generator
 * Plugin URI: https://github.com/itsumonotakumi/llms-txt-full-txt-generator
 * Description: Generate llms.txt and llms-full.txt files from your WordPress content for LLM training.
 * Version: 2.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Itsumonotakumi
 * Author URI: https://mobile-cheap.jp
 * Text Domain: llms-txt-full-txt-generator
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'admin-page.php';

class LLMS_TXT_Generator {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    public function add_admin_menu() {
        add_options_page(
            __('LLMS.txt Generator Settings', 'llms-txt-full-txt-generator'),
            __('LLMS.txt Generator', 'llms-txt-full-txt-generator'),
            'manage_options',
            'llms-txt-generator',
            array($this, 'admin_page')
        );
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'llms-txt-full-txt-generator',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    public function admin_page() {
        include plugin_dir_path(__FILE__) . 'admin-page.php';
    }
}

new LLMS_TXT_Generator();
