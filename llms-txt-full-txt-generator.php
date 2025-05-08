<?php
/*
Plugin Name: LLMS TXT and Full TXT Generator
Plugin URI: https://github.com/itsumonotakumi/llms-txt-full-txt-generator
Description: サイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力します。LLMの学習データとして利用できます。 | Outputs your site's content to llms.txt and llms-full.txt files for use as LLM training data.
Version: 1.9.2
Author: いつもの匠
Author URI: https://mobile-cheap.jp
License: GPL v2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: llms-txt-full-txt-generator
Domain Path: /languages
Contact: llms-txt@takulog.info
Homepage: https://mobile-cheap.jp
X: https://x.com/itsumonotakumi
Threads: https://www.threads.net/@itsumonotakumi
YouTube: https://www.youtube.com/@itsumonotakumi
*/

/**
 * LLMS TXT and Full TXT Generator
 *
 * サイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力します。
 *
 * @version 1.9.2
 * @author いつもの匠
 * @author rankth (Original Author)
 * @link https://mobile-cheap.jp
 */

// 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// プラグインの定数定義
define('LLMS_TXT_GENERATOR_VERSION', '1.9.2');
define('LLMS_TXT_GENERATOR_PATH', plugin_dir_path(__FILE__));
define('LLMS_TXT_GENERATOR_URL', plugin_dir_url(__FILE__));

/**
 * デバッグログを記録する関数
 * 
 * @param string $message ログメッセージ
 * @return void
 */
function llms_txt_generator_debug_log($message) {
    if (get_option('llms_txt_generator_debug_mode', false)) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
        error_log($message);
    }
}

/**
 * LLMS TXT and Full TXT Generator
 *
 * サイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力します。
 *
 * @version 1.9.2
 * @author rankth (Original Author)
 * @author いつもの匠 (Customized Version)
 * @link https://github.com/itsumonotakumi/llms-txt-full-txt-generator
 */
class LLMS_TXT_Generator {
    // シングルトンインスタンス
    private static $instance = null;

    /**
     * シングルトンインスタンスを取得
     *
     * @return LLMS_TXT_Generator
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     */
    private function __construct() {
        // 国際化対応
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // バージョン更新チェック（プラグインが読み込まれるたびに実行）
        $this->maybe_update();

        // 管理画面の設定
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_generate_llms_txt', array($this, 'handle_generate_llms_txt'));
        add_action('admin_post_llms_toggle_delete_settings', array($this, 'handle_toggle_delete_settings'));

        // フロントエンドに404エラーを出力しないようにファイルをチェック
        add_action('template_redirect', array($this, 'handle_txt_file_requests'));

        // ポストの追加・更新・削除時にファイルを更新するためのフックを追加
        add_action('save_post', array($this, 'auto_update_llms_files'), 10, 3);
        add_action('delete_post', array($this, 'auto_update_llms_files'));
        add_action('wp_trash_post', array($this, 'auto_update_llms_files'));
        add_action('untrash_post', array($this, 'auto_update_llms_files'));

        // スケジュール生成のためのフック
        add_action('llms_txt_generate_schedule', array($this, 'scheduled_generation'));
        add_action('update_option_llms_txt_generator_schedule_enabled', array($this, 'handle_schedule_option_update'), 10, 2);
        add_action('update_option_llms_txt_generator_schedule_frequency', array($this, 'handle_schedule_option_update'), 10, 2);

        // アンインストール時の処理
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));

        // プラグイン有効化時にスケジュールをセットアップとバージョン管理
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
    }

    /**
     * プラグイン有効化時の処理
     */
    public function plugin_activation() {
        // 既存の設定を確認し、存在しない場合はデフォルト値を設定
        $this->ensure_settings_exist();

        // クロンスケジュールのセットアップ
        $this->setup_schedule();

        // バージョン情報を更新
        update_option('llms_txt_generator_version', LLMS_TXT_GENERATOR_VERSION);

        llms_txt_generator_debug_log('LLMS TXT Generator: プラグインが有効化されました。バージョン: ' . LLMS_TXT_GENERATOR_VERSION);
    }

    /**
     * 必要に応じてプラグインをアップデート
     */
    private function maybe_update() {
        $current_version = get_option('llms_txt_generator_version', '0');

        // 初めてのインストールの場合
        if ($current_version === '0') {
            // 初期設定を適用
            update_option('llms_txt_generator_version', LLMS_TXT_GENERATOR_VERSION);

            // 既存の設定があるかチェック（プラグイン削除後の再インストール対応）
            if (get_option('llms_txt_generator_post_types') !== false) {
                // 既存の設定が見つかった場合、再インストールと判断
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
                if (get_option('llms_txt_generator_debug_mode', false)) {
                    error_log('LLMS TXT Generator: 既存の設定を検出しました。設定を引き継ぎます。');
                }
            } else {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
                // 完全に新規インストールの場合はデフォルト設定を適用
                if (get_option('llms_txt_generator_debug_mode', false)) {
                    error_log('LLMS TXT Generator: 新規インストールを検出しました。デフォルト設定を適用します。');
                }
                // デフォルト設定の適用
                $this->ensure_settings_exist();
            }
            return;
        }

                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
        // バージョンが異なる場合は更新処理
        if (version_compare($current_version, LLMS_TXT_GENERATOR_VERSION, '<')) {
            if (get_option('llms_txt_generator_debug_mode', false)) {
                error_log('LLMS TXT Generator: バージョン ' . $current_version . ' から ' . LLMS_TXT_GENERATOR_VERSION . ' にアップデートします。');
            }

                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
            // バージョン固有の移行処理をここに追加
            if (version_compare($current_version, '1.9.2', '<')) {
                // 1.9.2より前からのアップデートの場合の処理
                if (get_option('llms_txt_generator_debug_mode', false)) {
                    error_log('LLMS TXT Generator: 1.9.2より前のバージョンからのアップデート処理を実行します。');
                }

                // 設定が存在することを確認（念のため）
                $this->ensure_settings_exist();
            }

            // バージョン情報を更新
            update_option('llms_txt_generator_version', LLMS_TXT_GENERATOR_VERSION);
        }
    }

    /**
     * 設定が存在することを確認
     * プラグイン削除・再インストール時の設定引き継ぎを確実にするための処理
     */
    private function ensure_settings_exist() {
        // 主要設定のデフォルト値
        $default_settings = array(
            'llms_txt_generator_post_types' => array('post', 'page'),
            'llms_txt_generator_include_excerpt' => false,
            'llms_txt_generator_auto_update' => true,
            'llms_txt_generator_debug_mode' => false,
            'llms_txt_generator_schedule_enabled' => false,
            'llms_txt_generator_schedule_frequency' => 'daily',
            'llms_txt_generator_custom_header' => '',
            'llms_txt_generator_include_urls' => '',
            'llms_txt_generator_exclude_urls' => ''
        );

        // 各設定項目を確認し、存在しない場合はデフォルト値を設定
        foreach ($default_settings as $option_name => $default_value) {
            // 注意: get_option は false を返すが、オプションの値が false の場合もあるので、
            // 厳密に存在チェックをするために第三引数にユニークな値を指定する
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Only used when debug mode is enabled
            $value = get_option($option_name, '__not_exists__');
            if ($value === '__not_exists__') {
                // オプションが存在しない場合のみデフォルト値を追加
                add_option($option_name, $default_value);
                if (get_option('llms_txt_generator_debug_mode', false)) {
                    error_log('LLMS TXT Generator: 設定 ' . $option_name . ' が見つからないため、デフォルト値を設定しました。');
                }
            }
        }
    }

    /**
     * 翻訳ファイルを読み込む
     */
    public function load_textdomain() {
        load_plugin_textdomain('llms-txt-full-txt-generator', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * プラグイン無効化時の処理
     */
    public function plugin_deactivation() {
        // トランジェントを削除
        delete_transient('llms_files_updating');

        // タスクのスケジュールを解除
        $timestamp = wp_next_scheduled('llms_txt_generate_schedule');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'llms_txt_generate_schedule');
        }
    }

    /**
     * llms.txtファイルへのリクエストを処理
     */
    public function handle_txt_file_requests() {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $request_uri = wp_parse_url($request_uri, PHP_URL_PATH);

        if ($request_uri === '/llms.txt' || $request_uri === '/llms-full.txt') {
            $filename = ABSPATH . substr($request_uri, 1);

            if (file_exists($filename)) {
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                
                header('Content-Type: text/plain; charset=UTF-8');
                header('Content-Disposition: inline; filename="' . sanitize_file_name(basename($filename)) . '"');
                
                if ($wp_filesystem->exists($filename)) {
                    echo wp_kses_post($wp_filesystem->get_contents($filename));
                }
                exit;
            }
        }
    }

    /**
     * プラグイン設定の登録
     */
    public function register_settings() {
        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_post_types',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_post_types'),
                'default' => array()
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_include_excerpt',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_auto_update',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_debug_mode',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_schedule_enabled',
            array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_schedule_frequency',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'daily'
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_custom_header',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => ''
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_include_urls',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => ''
            )
        );

        register_setting(
            'llms_txt_generator_settings',
            'llms_txt_generator_exclude_urls',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => ''
            )
        );
    }

    /**
     * 投稿タイプの検証
     */
    public function sanitize_post_types($input) {
        if (empty($input)) {
            add_settings_error(
                'llms_txt_generator_post_types',
                'no_post_types_selected',
                __('設定を保存するには、少なくとも1つの投稿タイプを選択する必要があります。', 'llms-txt-full-txt-generator'),
                'error'
            );
            return get_option('llms_txt_generator_post_types', array());
        }
        return array_map('sanitize_text_field', $input);
    }

    /**
     * 管理メニューの追加
     */
    public function add_admin_menu() {
        add_options_page(
            __('LLMS TXT and Full TXT Generator 設定', 'llms-txt-full-txt-generator'),
            __('LLMS.txt生成設定', 'llms-txt-full-txt-generator'),
            'manage_options',
            'llms-txt-generator',
            array($this, 'admin_page')
        );
    }

    /**
     * 管理画面の表示
     */
    public function admin_page() {
        if (isset($_GET['llms_generated'])) {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'llms_generate_view')) {
                wp_die(esc_html__('セキュリティチェックに失敗しました。', 'llms-txt-full-txt-generator'));
            }
            
            if (sanitize_text_field(wp_unslash($_GET['llms_generated'])) === 'true') {
                add_settings_error('llms_txt_generator', 'files_generated', esc_html__('LLMS.txtファイルが正常に生成されました。', 'llms-txt-full-txt-generator'), 'updated');
            }
        }
        settings_errors('llms_txt_generator');
        include plugin_dir_path(__FILE__) . 'admin-page.php';
    }

    /**
     * llms.txtファイル生成リクエストの処理
     */
    public function handle_generate_llms_txt() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('このページにアクセスするための十分な権限がありません。', 'llms-txt-full-txt-generator'));
        }

        if (!isset($_POST['llms_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['llms_nonce'])), 'llms_generate_action')) {
            wp_die(esc_html__('無効なnonceが指定されました', 'llms-txt-full-txt-generator'));
        }

        $this->generate_llms_txt_files();

        wp_safe_redirect(add_query_arg('llms_generated', 'true', admin_url('options-general.php?page=llms-txt-generator')));
        exit;
    }

    /**
     * llms.txtとllms-full.txtファイルの生成
     *
     * @param bool $show_notification 通知メッセージを表示するかどうか
     */
    public function generate_llms_txt_files($show_notification = true) {
        $root_dir = ABSPATH;
        $llms_txt_path = $root_dir . '/llms.txt';
        $llms_full_txt_path = $root_dir . '/llms-full.txt';
        $selected_post_types = get_option('llms_txt_generator_post_types', array());
        $debug_mode = get_option('llms_txt_generator_debug_mode', false);

        if ($debug_mode) {
            $this->log_debug("--- ファイル生成開始 ---");
            $this->log_debug("選択された投稿タイプ: " . implode(', ', $selected_post_types));
        }

        if (empty($selected_post_types)) {
            // WP_Filesystemを使用してファイルを安全に書き込む
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            
            $wp_filesystem->put_contents($llms_txt_path, '', FS_CHMOD_FILE);
            $wp_filesystem->put_contents($llms_full_txt_path, '', FS_CHMOD_FILE);
            
            if ($show_notification) {
                add_settings_error('llms_txt_generator', 'no_post_types', __('投稿タイプが選択されていません。llms.txtとllms-full.txtの両方がクリアされました。', 'llms-txt-full-txt-generator'), 'updated');
            }
            if ($debug_mode) {
                $this->log_debug("投稿タイプが選択されていないため、ファイルをクリアして終了");
            }
            return;
        }

        if (file_exists($llms_txt_path)) {
            wp_delete_file($llms_txt_path);
        }
        if (file_exists($llms_full_txt_path)) {
            wp_delete_file($llms_full_txt_path);
        }

        $utf8_bom = chr(239) . chr(187) . chr(191);

        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        $llms_txt_content = "# {$site_name}\n\n> {$site_description}\n\n";
        $llms_full_txt_content = $llms_txt_content;

        $custom_header = get_option('llms_txt_generator_custom_header', '');
        if (!empty($custom_header)) {
            $llms_txt_content .= $custom_header . "\n\n";
            $llms_full_txt_content .= $custom_header . "\n\n";
        }

        $include_excerpt = get_option('llms_txt_generator_include_excerpt', false);
        $include_urls = $this->parse_url_rules(get_option('llms_txt_generator_include_urls', ''));
        $exclude_urls = $this->parse_url_rules(get_option('llms_txt_generator_exclude_urls', ''));

        foreach ($selected_post_types as $post_type) {
            // 投稿タイプの情報を取得
            $post_type_obj = get_post_type_object($post_type);
            $post_type_name = $post_type_obj ? $post_type_obj->labels->name : $post_type;

            // 投稿タイプの見出しを追加
            $llms_txt_content .= "## {$post_type_name}\n\n";
            $llms_full_txt_content .= "## {$post_type_name}\n\n";

            $posts = get_posts(array('post_type' => $post_type, 'posts_per_page' => -1));
            $added_posts = 0;

            foreach ($posts as $post) {
                $post_url = get_permalink($post->ID);

                // URLの除外処理を修正
                if ($this->should_include_url($post_url, $include_urls, $exclude_urls)) {
                    $title = esc_html($post->post_title);
                    $content = wp_strip_all_tags($post->post_content);
                    $llms_txt_content .= "- [{$title}](" . esc_url($post_url) . ")\n";
                    $full_entry = "### {$title}\n\n{$content}\n\n";
                    if ($include_excerpt && !empty($post->post_excerpt)) {
                        $excerpt = wp_strip_all_tags($post->post_excerpt);
                        $full_entry .= "Excerpt: {$excerpt}\n\n";
                    }
                    $llms_full_txt_content .= "{$full_entry}";
                    $added_posts++;
                }
            }

            // 投稿が追加されなかった場合のメッセージ
            if ($added_posts === 0) {
                $llms_txt_content .= __('該当する投稿がありません。', 'llms-txt-full-txt-generator') . "\n";
                $llms_full_txt_content .= __('該当する投稿がありません。', 'llms-txt-full-txt-generator') . "\n";
            }

            $llms_txt_content .= "\n";
            $llms_full_txt_content .= "\n";
        }

        // WP_Filesystemを使用してファイルを安全に書き込む
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        
        $llms_txt_content_encoded = $utf8_bom . mb_convert_encoding($llms_txt_content, 'UTF-8');
        $llms_full_txt_content_encoded = $utf8_bom . mb_convert_encoding($llms_full_txt_content, 'UTF-8');
        
        $wp_filesystem->put_contents($llms_txt_path, $llms_txt_content_encoded, FS_CHMOD_FILE);
        $wp_filesystem->put_contents($llms_full_txt_path, $llms_full_txt_content_encoded, FS_CHMOD_FILE);
        
        if ($show_notification) {
            add_settings_error('llms_txt_generator', 'files_generated', __('LLMS.txtファイルが正常に生成されました。', 'llms-txt-full-txt-generator'), 'updated');
        }
    }

    /**
     * URLルールを解析する
     *
     * @param string $rules_string 改行区切りのURLルール
     * @return array 正規化されたURLルールの配列
     */
    private function parse_url_rules($rules_string) {
        // ルールをパースした後、各ルールも正規化する（末尾のスラッシュを削除）
        $rules = array_filter(array_map('trim', explode("\n", sanitize_textarea_field($rules_string))));

        // 各ルールを正規化
        foreach ($rules as $key => $rule) {
            $rules[$key] = rtrim($rule, '/');
        }

        return $rules;
    }

    /**
     * URLが含めるべきかどうかを判断する
     *
     * @param string $url 確認するURL
     * @param array $include_rules 含めるURLルールの配列
     * @param array $exclude_rules 除外するURLルールの配列
     * @return bool URLを含めるべきかどうか
     */
    private function should_include_url($url, $include_rules, $exclude_rules) {
        $debug_mode = get_option('llms_txt_generator_debug_mode', false);
        $debug_log = '';

        // デバッグモードが有効な場合はログ開始
        if ($debug_mode) {
            $debug_log .= "-- URL検証開始: {$url} --\n";
        }

        // URLの正規化（末尾のスラッシュを一貫して処理）
        $url = rtrim($url, '/');

        // 相対URLと絶対URLの両方をチェックできるようにする
        $relative_url = wp_make_link_relative($url);
        $absolute_url = $url;

        if ($debug_mode) {
            $debug_log .= "正規化後のURL: {$url}\n";
            $debug_log .= "相対URL: {$relative_url}\n";
            $debug_log .= "絶対URL: {$absolute_url}\n";
            $debug_log .= "除外ルール数: " . count($exclude_rules) . "\n";
            $debug_log .= "包含ルール数: " . count($include_rules) . "\n";
        }

        // 除外URLのルールをチェック - 一つでもマッチしたら除外
        foreach ($exclude_rules as $rule) {
            $rule = trim($rule);
            if (empty($rule)) {
                continue;
            }

            if ($debug_mode) {
                $debug_log .= "除外ルールチェック: '{$rule}'\n";
            }

            // ワイルドカードなしの完全一致をまず確認
            if ($rule === $relative_url || $rule === $absolute_url) {
                if ($debug_mode) {
                    $debug_log .= "→ 完全一致で除外されました\n";
                    $this->log_debug($debug_log);
                }
                return false;
            }

            // ワイルドカードを含む場合は正規表現でチェック
            if (strpos($rule, '*') !== false) {
                $pattern = $this->convert_wildcard_to_regex($rule);
                if ($debug_mode) {
                    $debug_log .= "→ ワイルドカードルール変換: {$pattern}\n";
                }

                if (preg_match($pattern, $relative_url)) {
                    if ($debug_mode) {
                        $debug_log .= "→ 相対URLがパターンにマッチして除外されました\n";
                        $this->log_debug($debug_log);
                    }
                    return false;
                }

                if (preg_match($pattern, $absolute_url)) {
                    if ($debug_mode) {
                        $debug_log .= "→ 絶対URLがパターンにマッチして除外されました\n";
                        $this->log_debug($debug_log);
                    }
                    return false;
                }
            }
        }

        // 含めるURLのルールが空の場合はすべてを含める
        if (empty($include_rules)) {
            if ($debug_mode) {
                $debug_log .= "包含ルールが空のため、URLは含まれます\n";
                $this->log_debug($debug_log);
            }
            return true;
        }

        // 含めるURLのルールをチェック - 一つでもマッチしたら含める
        foreach ($include_rules as $rule) {
            $rule = trim($rule);
            if (empty($rule)) {
                continue;
            }

            if ($debug_mode) {
                $debug_log .= "包含ルールチェック: '{$rule}'\n";
            }

            // ワイルドカードなしの完全一致をまず確認
            if ($rule === $relative_url || $rule === $absolute_url) {
                if ($debug_mode) {
                    $debug_log .= "→ 完全一致で含まれました\n";
                    $this->log_debug($debug_log);
                }
                return true;
            }

            // ワイルドカードを含む場合は正規表現でチェック
            if (strpos($rule, '*') !== false) {
                $pattern = $this->convert_wildcard_to_regex($rule);
                if ($debug_mode) {
                    $debug_log .= "→ ワイルドカードルール変換: {$pattern}\n";
                }

                if (preg_match($pattern, $relative_url)) {
                    if ($debug_mode) {
                        $debug_log .= "→ 相対URLがパターンにマッチして含まれました\n";
                        $this->log_debug($debug_log);
                    }
                    return true;
                }

                if (preg_match($pattern, $absolute_url)) {
                    if ($debug_mode) {
                        $debug_log .= "→ 絶対URLがパターンにマッチして含まれました\n";
                        $this->log_debug($debug_log);
                    }
                    return true;
                }
            }
        }

        if ($debug_mode) {
            $debug_log .= "どのルールにもマッチしなかったため、URLは除外されました\n";
            $this->log_debug($debug_log);
        }
        return false;
    }

    /**
     * デバッグログを記録する
     *
     * @param string $message ログメッセージ
     */
    private function log_debug($message) {
        // デバッグモードが有効な場合のみログを記録
        if (!get_option('llms_txt_generator_debug_mode', false)) {
            return;
        }
        
        $log_dir = LLMS_TXT_GENERATOR_PATH . 'logs';
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        
        $log_file = $log_dir . '/url_debug.log';
        $timestamp = current_time('mysql');
        $log_content = mb_convert_encoding("[{$timestamp}] {$message}\n", 'UTF-8');
        
        if ($wp_filesystem->exists($log_dir) || $wp_filesystem->mkdir($log_dir, FS_CHMOD_DIR)) {
            $wp_filesystem->put_contents($log_file, $log_content, FS_CHMOD_FILE | FILE_APPEND);
        }
    }

    /**
     * ワイルドカードを含む文字列を正規表現パターンに変換
     *
     * @param string $rule 変換するワイルドカードパターン
     * @return string 正規表現パターン
     */
    private function convert_wildcard_to_regex($rule) {
        // 正規表現の特殊文字をエスケープ
        $rule = preg_quote($rule, '/');

        // アスタリスクを正規表現のワイルドカードに変換
        $rule = str_replace('\*', '.*', $rule);

        // 完全一致を確認する正規表現パターンを返す
        return '/^' . $rule . '$/i';
    }

    /**
     * URLがルールにマッチするかを確認（後方互換性のため維持）
     *
     * @param string $url チェックするURL
     * @param string $rule 検証するルール
     * @return bool マッチするかどうか
     */
    private function match_url_rule($url, $rule) {
        // 非推奨: 新しいconvert_wildcard_to_regexとshould_include_urlを使用
        $pattern = $this->convert_wildcard_to_regex(trim($rule));
        return preg_match($pattern, trim($url));
    }

    /**
     * 投稿の変更時に自動的にllms.txtファイルを更新
     *
     * @param int $post_id 投稿ID
     * @param WP_Post|null $post 投稿オブジェクト
     * @param bool|null $update 更新かどうか
     */
    public function auto_update_llms_files($post_id, $post = null, $update = null) {
        // 自動保存の場合は処理しない
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // リビジョンの場合は処理しない
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // 自動更新が無効になっている場合は処理しない
        if (!get_option('llms_txt_generator_auto_update', true)) {
            return;
        }

        // 投稿タイプを取得
        $post_type = get_post_type($post_id);
        if (!$post_type) {
            return;
        }

        // 選択された投稿タイプかどうかを確認
        $selected_post_types = get_option('llms_txt_generator_post_types', array());
        if (!in_array($post_type, $selected_post_types)) {
            return;
        }

        // 更新が連続して行われないようにディレイを設定
        if (!get_transient('llms_files_updating')) {
            // 3秒間のトランジェントを設定
            set_transient('llms_files_updating', true, 3);

            // llms.txtファイルを更新
            $this->generate_llms_txt_files(false);
        }
    }

    /**
     * 設定の変更時にスケジュールを更新
     *
     * @param mixed $old_value 古い値
     * @param mixed $new_value 新しい値
     */
    public function handle_schedule_option_update($old_value, $new_value) {
        $this->setup_schedule();
    }

    /**
     * スケジュールをセットアップ
     */
    public function setup_schedule() {
        // 既存のスケジュールをクリア
        $timestamp = wp_next_scheduled('llms_txt_generate_schedule');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'llms_txt_generate_schedule');
        }

        // スケジュールが有効な場合は新しいスケジュールを設定
        if (get_option('llms_txt_generator_schedule_enabled', false)) {
            $frequency = get_option('llms_txt_generator_schedule_frequency', 'daily');
            wp_schedule_event(time(), $frequency, 'llms_txt_generate_schedule');
        }
    }

    /**
     * スケジュールされた生成処理
     */
    public function scheduled_generation() {
        $this->generate_llms_txt_files(false);
    }

    /**
     * 設定削除オプションの切り替えを処理
     */
    public function handle_toggle_delete_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('このページにアクセスするための十分な権限がありません。', 'llms-txt-full-txt-generator'));
        }

        if (!isset($_POST['llms_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['llms_settings_nonce'])), 'llms_settings_action')) {
            wp_die(esc_html__('無効なnonceが指定されました', 'llms-txt-full-txt-generator'));
        }

        $delete_all_data = isset($_POST['llms_delete_all_data']) ? true : false;
        update_option('llms_txt_generator_delete_all_data', $delete_all_data);

        // 設定ページにリダイレクト
        wp_safe_redirect(add_query_arg('page', 'llms-txt-generator', admin_url('options-general.php')) . '#help-tab');
        exit;
    }
}

/**
 * プラグインのインスタンスを初期化して取得する関数
 *
 * @return LLMS_TXT_Generator プラグインのインスタンス
 */
function llms_txt_generator() {
    return LLMS_TXT_Generator::get_instance();
}

// プラグインを起動
llms_txt_generator();
