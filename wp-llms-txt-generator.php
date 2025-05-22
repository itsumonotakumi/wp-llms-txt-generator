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

        // llms.txtファイルへのリクエストをフック
        add_action('init', array($this, 'handle_view_llms_txt_files'));
    }

    public function add_admin_menu() {
        add_options_page(
            __('WP LLMS TXT Generator Settings', 'wp-llms-txt-generator'),
            __('WP LLMS TXT Generator', 'wp-llms-txt-generator'),
            'manage_options',
            'llms-txt-generator',
            array($this, 'admin_page')
        );
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'wp-llms-txt-generator',
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
        // 権限チェック
        if (!current_user_can('manage_options')) {
            wp_die(__('この操作を実行する権限がありません。', 'wp-llms-txt-generator'));
        }

        // nonceチェック
        if (!isset($_POST['llms_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['llms_nonce']), 'llms_generate_action')) {
            wp_die(__('セキュリティチェックに失敗しました。', 'wp-llms-txt-generator'));
        }

        // llms.txtとllms-full.txtファイルの生成処理
        $this->generate_llms_txt_files();

        // リダイレクト
        wp_redirect(admin_url('options-general.php?page=llms-txt-generator&tab=generate&generated=1'));
        exit;
    }

    /**
     * llms.txtとllms-full.txtファイルを生成する
     */
    private function generate_llms_txt_files() {
        global $wp_rewrite;

        $root_dir = untrailingslashit(ABSPATH);
        $llms_txt_path = wp_normalize_path($root_dir . '/llms.txt');
        $llms_full_txt_path = wp_normalize_path($root_dir . '/llms-full.txt');
        
        $normalized_abspath = wp_normalize_path(untrailingslashit(ABSPATH));
        if (strpos($llms_txt_path, $normalized_abspath) !== 0 || strpos($llms_full_txt_path, $normalized_abspath) !== 0) {
            return false;
        }

        // 設定の取得
        $selected_post_types = get_option('llms_txt_generator_post_types', array('post', 'page'));
        $custom_header = get_option('llms_txt_generator_custom_header', '');
        $include_excerpt = get_option('llms_txt_generator_include_excerpt', false);
        $include_urls = get_option('llms_txt_generator_include_urls', '');
        $exclude_urls = get_option('llms_txt_generator_exclude_urls', '');

        // 含めるURLと除外するURLの配列を作成
        $include_urls_array = !empty($include_urls) ? array_map('trim', explode("\n", $include_urls)) : array();
        $exclude_urls_array = !empty($exclude_urls) ? array_map('trim', explode("\n", $exclude_urls)) : array();

        // ファイルの内容
        $llms_txt_content = '';
        $llms_full_txt_content = '';

        // UTF-8宣言
        $utf8_declaration = "# Encoding: UTF-8\n";
        $llms_txt_content .= $utf8_declaration;
        $llms_full_txt_content .= $utf8_declaration;

        // カスタムヘッダーの追加
        if (!empty($custom_header)) {
            $custom_header = $this->ensure_utf8($custom_header);
            $llms_txt_content .= $custom_header . "\n\n";
            $llms_full_txt_content .= $custom_header . "\n\n";
        }

        // 選択された各投稿タイプについて処理
        foreach ($selected_post_types as $post_type) {
            $post_type_obj = get_post_type_object($post_type);
            $post_type_name = $post_type_obj ? $post_type_obj->labels->name : $post_type;

            // 投稿タイプの見出しを追加
            $llms_txt_content .= "# " . $post_type_name . "\n\n";
            $llms_full_txt_content .= "# " . $post_type_name . "\n\n";

            // 投稿を取得
            $args = array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $post_id = get_the_ID();
                    $permalink = get_permalink($post_id);
                    $title = get_the_title();
                    $content = get_the_content();
                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words($content, 55, '...');

                    // UTF-8エンコーディングを保証
                    $permalink = $this->ensure_utf8($permalink);
                    $title = $this->ensure_utf8($title);
                    $content = $this->ensure_utf8($content);
                    $excerpt = $this->ensure_utf8($excerpt);

                    // URLフィルタリング
                    if (!$this->is_url_allowed($permalink, $include_urls_array, $exclude_urls_array)) {
                        continue;
                    }

                    // llms.txtにはURLのみ追加
                    $llms_txt_content .= $permalink . "\n";

                    // llms-full.txtにはURLとコンテンツを追加
                    $llms_full_txt_content .= $title . "\n";
                    $llms_full_txt_content .= $permalink . "\n";

                    if ($include_excerpt) {
                        $llms_full_txt_content .= $excerpt . "\n\n";
                    } else {
                        // 本文のHTMLタグを除去して追加
                        $content = wp_strip_all_tags($content);
                        $content = str_replace(array("\r\n", "\r", "\n"), "\n", $content);
                        $content = preg_replace("/\n\s+\n/", "\n\n", $content);
                        $content = preg_replace("/\n{3,}/", "\n\n", $content);
                        $llms_full_txt_content .= $content . "\n\n";
                    }
                }
            }

            wp_reset_postdata();

            // 投稿タイプ間の区切り
            $llms_txt_content .= "\n";
            $llms_full_txt_content .= "\n";
        }

        // ファイルの書き込み - UTF-8でエンコード
        $bom = chr(239) . chr(187) . chr(191); // UTF-8 BOM
        
        $root_dir_writable = wp_is_writable($root_dir);
        if (!$root_dir_writable) {
            return false;
        }
        
        // ファイルの書き込み
        $llms_txt_result = file_put_contents($llms_txt_path, $bom . $llms_txt_content);
        $llms_full_txt_result = file_put_contents($llms_full_txt_path, $bom . $llms_full_txt_content);
        
        return ($llms_txt_result !== false && $llms_full_txt_result !== false);
    }

    /**
     * 文字列がUTF-8エンコーディングであることを確認
     */
    private function ensure_utf8($str) {
        // 文字列がnullまたは空の場合は空文字を返す
        if (empty($str)) {
            return '';
        }

        // 現在のエンコーディングを検出
        $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1, EUC-JP, SJIS, GB2312, BIG5', true);

        // エンコーディングが検出できない場合はUTF-8と仮定
        if ($encoding === false) {
            $encoding = 'UTF-8';
        }

        // UTF-8でない場合は変換
        if ($encoding !== 'UTF-8') {
            $str = mb_convert_encoding($str, 'UTF-8', $encoding);
        }

        // BOMがあれば削除
        $bom = pack('H*', 'EFBBBF');
        $str = preg_replace("/^$bom/", '', $str);

        return $str;
    }

    /**
     * URLがフィルター設定に基づいて許可されているかをチェック
     */
    private function is_url_allowed($url, $include_urls_array, $exclude_urls_array) {
        // URLの正規化（末尾のスラッシュを削除）
        $url = rtrim($url, '/');

        // デバッグモードが有効な場合、ログを記録
        $debug_mode = get_option('llms_txt_generator_debug_mode', false);
        if ($debug_mode) {
            $log_dir = plugin_dir_path(__FILE__) . 'logs/';
            if (!file_exists($log_dir)) {
                wp_mkdir_p($log_dir);
                file_put_contents($log_dir . '.htaccess', "Order deny,allow\nDeny from all");
            }
            $log_file = $log_dir . 'url_debug.log';
            if (is_writable($log_dir)) {
                file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "Processing URL: " . esc_url($url) . "\n", FILE_APPEND);
            }
        }

        // 除外URLのチェック
        if (!empty($exclude_urls_array)) {
            foreach ($exclude_urls_array as $pattern) {
                $pattern = rtrim(trim($pattern), '/');
                if (empty($pattern)) continue;

                $regex = $this->wildcard_to_regex($pattern);
                if (preg_match($regex, $url)) {
                    if ($debug_mode) {
                        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "URL excluded by pattern: {$pattern}\n", FILE_APPEND);
                    }
                    return false;
                }
            }
        }

        // 含めるURLのチェック（設定がある場合のみ）
        if (!empty($include_urls_array)) {
            $included = false;
            foreach ($include_urls_array as $pattern) {
                $pattern = rtrim(trim($pattern), '/');
                if (empty($pattern)) continue;

                $regex = $this->wildcard_to_regex($pattern);
                if (preg_match($regex, $url)) {
                    $included = true;
                    if ($debug_mode) {
                        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "URL included by pattern: {$pattern}\n", FILE_APPEND);
                    }
                    break;
                }
            }

            if (!$included) {
                if ($debug_mode) {
                    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "URL not included in any pattern\n", FILE_APPEND);
                }
                return false;
            }
        }

        return true;
    }

    /**
     * ワイルドカードパターンを正規表現に変換
     */
    private function wildcard_to_regex($pattern) {
        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace('\*', '.*', $pattern);
        return '/^' . $pattern . '$/i';
    }

    /**
     * llms.txtファイルを表示するためのリクエストを処理
     */
    public function handle_view_llms_txt_files() {
        // llms.txt または llms-full.txt へのリクエストかどうかを確認
        $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field($_SERVER['REQUEST_URI']) : '';

        if (preg_match('/\/llms(-full)?\.txt$/', $request_uri, $matches)) {
            $is_full = !empty($matches[1]);
            $file_path = ABSPATH . ($is_full ? 'llms-full.txt' : 'llms.txt');
            
            $normalized_path = wp_normalize_path($file_path);
            $normalized_abspath = wp_normalize_path(ABSPATH);
            
            if (file_exists($file_path) && strpos($normalized_path, $normalized_abspath) === 0) {
                // キャッシュを無効化
                nocache_headers();
                
                // 適切なヘッダーを送信
                header('Content-Type: text/plain; charset=UTF-8');
                header('Content-Length: ' . filesize($file_path));
                header('Content-Disposition: inline; filename=' . basename($file_path));
                
                // ファイルを出力して終了
                readfile($file_path);
                exit;
            }
        }
    }
}

new LLMS_TXT_Generator();
