<?php
/**
 * Plugin Name: WP LLMS TXT Generator
 * Plugin URI: https://github.com/itsumonotakumi/wp-llms-txt-generator
 * Description: WordPressサイトのコンテンツをLLM学習用データとして出力するためのプラグイン
 * Version: 2.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Itsumonotakumi
 * Author URI: https://mobile-cheap.jp
 * Text Domain: wp-llms-txt-generator
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin file constant
if (!defined('WP_LLMS_TXT_GENERATOR_FILE')) {
    define('WP_LLMS_TXT_GENERATOR_FILE', __FILE__);
}

// Load Composer autoloader if it exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Fallback manual autoloader
    spl_autoload_register(function ($class) {
        // Project-specific namespace prefix
        $prefix = 'ItsumonoTakumi\\WpLlmsTxtGenerator\\';
        
        // Base directory for the namespace prefix
        $base_dir = __DIR__ . '/src/';
        
        // Does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // No, move to the next registered autoloader
            return;
        }
        
        // Get the relative class name
        $relative_class = substr($class, $len);
        
        // Replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        // If the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    });
}

// 管理画面関連の関数が確実に読み込まれているようにする
if (is_admin()) {
    require_once(ABSPATH . 'wp-admin/includes/template.php');
}

// Initialize the plugin
if (class_exists('ItsumonoTakumi\\WpLlmsTxtGenerator\\Core\\Generator')) {
    // Create an instance of the main plugin class
    new ItsumonoTakumi\WpLlmsTxtGenerator\Core\Generator();
} else {
    // Fallback to legacy class if autoloading fails
    require_once plugin_dir_path(__FILE__) . 'wp-llms-txt-generator-legacy.php';
}
