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

// 管理画面関連の関数が確実に読み込まれているようにする
if (is_admin()) {
    require_once(ABSPATH . 'wp-admin/includes/template.php');
}

// admin-page.phpを直接読み込まないように修正
// 管理画面表示関数内で必要なときに読み込むようにする
// require_once plugin_dir_path(__FILE__) . 'admin-page.php';

class LLMS_TXT_Generator {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // 設定を登録
        add_action('admin_init', array($this, 'register_settings'));

        // admin-post.phpで処理するアクションフックを追加
        add_action('admin_post_generate_llms_txt', array($this, 'handle_generate_llms_txt'));
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
        // 必要なWordPress管理画面関数を確認
        if (!function_exists('do_settings_sections')) {
            require_once(ABSPATH . 'wp-admin/includes/template.php');
        }

        // ここでadmin-page.phpをincludeする
        include_once plugin_dir_path(__FILE__) . 'admin-page.php';

        // 管理画面のHTMLを出力する関数を呼び出す
        llms_txt_generator_admin_page_content();
    }

    public function register_settings() {
        // 設定を登録
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

    public function handle_generate_llms_txt() {
        // 将来的にLLMS.txtファイル生成処理を実装
        // 今はリダイレクトのみ
        wp_redirect(admin_url('options-general.php?page=llms-txt-generator&tab=generate'));
        exit;
    }
}

new LLMS_TXT_Generator();
