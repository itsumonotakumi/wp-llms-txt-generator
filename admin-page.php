<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!is_admin()) {
    return;
}

if (!function_exists('do_settings_sections')) {
    require_once(ABSPATH . 'wp-admin/includes/template.php');
}


/**
 * Render the admin page content for the plugin
 *
 * Creates a tabbed interface with settings, generation, and help tabs.
 *
 * @since 1.0.0
 * @return void
 */
function llms_txt_generator_admin_page_content() {
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="nav-tab-wrapper">
        <a href="#settings-tab" id="settings-tab-link" class="nav-tab nav-tab-active"><?php esc_html_e('設定', 'wp-llms-txt-generator'); ?></a>
        <a href="#generate-tab" id="generate-tab-link" class="nav-tab"><?php esc_html_e('生成', 'wp-llms-txt-generator'); ?></a>
        <a href="#help-tab" id="help-tab-link" class="nav-tab"><?php esc_html_e('ヘルプ', 'wp-llms-txt-generator'); ?></a>
    </div>

    <div id="settings-tab" class="tab-content">
        <form method="post" action="options.php">
            <?php
            settings_fields('llms_txt_generator_settings');
            ?>

            <h2><?php esc_html_e('基本設定', 'wp-llms-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('投稿タイプを選択', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <?php
                        $post_types = get_post_types(array('public' => true), 'objects');
                        $selected_post_types = get_option('llms_txt_generator_post_types', array());
                        foreach ($post_types as $post_type) {
                            $checked = in_array($post_type->name, $selected_post_types) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="llms_txt_generator_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
                        }
                        ?>
                        <p class="description"><?php esc_html_e('生成するファイルに含める投稿タイプを選択してください。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('カスタムヘッダーテキスト', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_custom_header" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_custom_header')); ?></textarea>
                        <p class="description"><?php esc_html_e('このテキストはllms.txtとllms-full.txtファイルのURLリストの上に表示されます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('抜粋を含める', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_include_excerpt" value="1" <?php checked(1, get_option('llms_txt_generator_include_excerpt'), true); ?> />
                        <p class="description"><?php esc_html_e('llms-full.txtファイルに投稿の抜粋を含めます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('更新設定', 'wp-llms-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('投稿の変更時に自動更新', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_auto_update" value="1" <?php checked(1, get_option('llms_txt_generator_auto_update', true), true); ?> />
                        <p class="description"><?php esc_html_e('有効にすると、投稿の追加・更新・削除時に自動的にLLMS.txtファイルが更新されます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('定期的に自動生成', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_schedule_enabled" value="1" <?php checked(1, get_option('llms_txt_generator_schedule_enabled', false), true); ?> />
                        <p class="description"><?php esc_html_e('有効にすると、指定した頻度でLLMS.txtファイルが自動的に生成されます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top" id="schedule-frequency-row">
                    <th scope="row"><?php esc_html_e('自動生成の頻度', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <select name="llms_txt_generator_schedule_frequency">
                            <?php
                            $frequency = get_option('llms_txt_generator_schedule_frequency', 'daily');
                            $schedules = array(
                                'hourly' => __('毎時', 'wp-llms-txt-generator'),
                                'twicedaily' => __('1日2回', 'wp-llms-txt-generator'),
                                'daily' => __('毎日', 'wp-llms-txt-generator'),
                                'weekly' => __('毎週', 'wp-llms-txt-generator')
                            );

                            foreach ($schedules as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    selected($frequency, $value, false),
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description"><?php esc_html_e('LLMSファイルを自動生成する頻度を選択してください。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('フィルター設定', 'wp-llms-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('含めるURL', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_include_urls" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_include_urls')); ?></textarea>
                        <p class="description"><?php esc_html_e('含めるURLを1行に1つずつ入力してください。*をワイルドカードとして使用できます。空の場合はすべてのURLが含まれます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('除外するURL', 'wp-llms-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_exclude_urls" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_exclude_urls')); ?></textarea>
                        <p class="description"><?php esc_html_e('除外するURLを1行に1つずつ入力してください。*をワイルドカードとして使用できます。', 'wp-llms-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('設定を保存', 'wp-llms-txt-generator')); ?>
        </form>
    </div>

    <div id="generate-tab" class="tab-content">
        <h2><?php esc_html_e('WP LLMS TXT Generator ファイルを生成', 'wp-llms-txt-generator'); ?></h2>

        <?php
        if (isset($_GET['generated']) && absint($_GET['generated']) === 1) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            esc_html_e('LLMS.txtファイルと LLMS-Full.txtファイルが正常に生成されました。', 'wp-llms-txt-generator');
            echo '</p></div>';
        }
        ?>

        <p><?php esc_html_e('下のボタンをクリックすると、現在の設定に基づいてllms.txtとllms-full.txtファイルを生成します。', 'wp-llms-txt-generator'); ?></p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('llms_generate_action', 'llms_nonce'); ?>
            <input type="hidden" name="action" value="generate_llms_txt">
            <input type="submit" name="generate_llms_txt" class="button button-primary" value="<?php echo esc_attr__('WP LLMS TXT Generator ファイルを生成', 'wp-llms-txt-generator'); ?>">
        </form>

        <div class="llms-file-status" style="margin-top: 20px;">
            <h3><?php esc_html_e('ファイルステータス', 'wp-llms-txt-generator'); ?></h3>
            <?php
            $root_dir = untrailingslashit(ABSPATH);
            $llms_txt_path = wp_normalize_path($root_dir . '/llms.txt');
            $llms_full_txt_path = wp_normalize_path($root_dir . '/llms-full.txt');
            
            $normalized_abspath = wp_normalize_path(untrailingslashit(ABSPATH));
            if (strpos($llms_txt_path, $normalized_abspath) !== 0 || strpos($llms_full_txt_path, $normalized_abspath) !== 0) {
                return;
            }

            if (file_exists($llms_txt_path)) {
                $llms_txt_url = home_url('/llms.txt');
                $modified = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), filemtime($llms_txt_path));
                echo '<div class="file-info">';
                /* translators: %s: URL to the llms.txt file */
                printf(
                    esc_html__('LLMS.txtファイル: %s', 'wp-llms-txt-generator'),
                    '<a href="' . esc_url($llms_txt_url) . '" target="_blank">' . esc_html($llms_txt_url) . '</a>'
                );
                echo '<p>';
                /* translators: %s: Last modified date and time of the file */
                printf(
                    esc_html__('最終更新日時: %s', 'wp-llms-txt-generator'),
                    esc_html($modified)
                );
                echo '</p><p>';
                /* translators: %s: File size in human readable format */
                printf(
                    esc_html__('ファイルサイズ: %s', 'wp-llms-txt-generator'),
                    esc_html(size_format(filesize($llms_txt_path)))
                );
                echo '</p></div>';
            } else {
                echo '<p>' . esc_html__('LLMS.txtファイルはまだ生成されていません。', 'wp-llms-txt-generator') . '</p>';
            }

            if (file_exists($llms_full_txt_path)) {
                $llms_full_txt_url = home_url('/llms-full.txt');
                $modified = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), filemtime($llms_full_txt_path));
                echo '<div class="file-info">';
                /* translators: %s: URL to the llms-full.txt file */
                printf(
                    esc_html__('LLMS-Full.txtファイル: %s', 'wp-llms-txt-generator'),
                    '<a href="' . esc_url($llms_full_txt_url) . '" target="_blank">' . esc_html($llms_full_txt_url) . '</a>'
                );
                echo '<p>';
                /* translators: %s: Last modified date and time of the file */
                printf(
                    esc_html__('最終更新日時: %s', 'wp-llms-txt-generator'),
                    esc_html($modified)
                );
                echo '</p><p>';
                /* translators: %s: File size in human readable format */
                printf(
                    esc_html__('ファイルサイズ: %s', 'wp-llms-txt-generator'),
                    esc_html(size_format(filesize($llms_full_txt_path)))
                );
                echo '</p></div>';
            } else {
                echo '<p>' . esc_html__('LLMS-Full.txtファイルはまだ生成されていません。', 'wp-llms-txt-generator') . '</p>';
            }
            ?>
        </div>
    </div>

    <div id="help-tab" class="tab-content">
        <h2><?php esc_html_e('ヘルプとサポート', 'wp-llms-txt-generator'); ?></h2>

        <div class="card">
            <h3><?php esc_html_e('このプラグインについて', 'wp-llms-txt-generator'); ?></h3>
            <p><?php esc_html_e('このプラグインはサイト内のコンテンツをllms.txtとllms-full.txtファイルに出力します。LLMの学習データとして利用できます。', 'wp-llms-txt-generator'); ?></p>
            <p><?php esc_html_e('このプラグインは元々、rankthによって開発されたLLMs-Full.txt and LLMs.txt Generatorをベースに、いつもの匠によって機能拡張されたものです。', 'wp-llms-txt-generator'); ?></p>
            <?php
            /* translators: %s: URL to the GitHub repository */
            echo '<p>' . sprintf(__('ソースコードは<a href="%s" target="_blank">GitHub</a>で公開されています。', 'wp-llms-txt-generator'), esc_url('https://github.com/itsumonotakumi/wp-llms-txt-generator')) . '</p>';
            /* translators: %s: URL to the original plugin on WordPress.org */
            echo '<p>' . sprintf(__('元のプラグイン: <a href="%s" target="_blank">LLMs-Full.txt and LLMs.txt Generator</a>', 'wp-llms-txt-generator'), esc_url('https://wordpress.org/plugins/llms-full-txt-generator/')) . '</p>';
            ?>

            <h4><?php esc_html_e('免責事項', 'wp-llms-txt-generator'); ?></h4>
            <p class="notice notice-warning" style="padding: 10px; margin: 10px 0;"><?php esc_html_e('このプログラムによるいかなる不都合や事故も補償しません。ご使用は自己責任でお願いします。', 'wp-llms-txt-generator'); ?></p>

            <h4><?php esc_html_e('連絡先情報', 'wp-llms-txt-generator'); ?></h4>
            <ul>
                <?php
                /* translators: %1$s: Email address for mailto link, %2$s: Email address for display */
                echo '<li>' . sprintf(__('メールアドレス: <a href="mailto:%1$s">%2$s</a>', 'wp-llms-txt-generator'), esc_attr('llms-txt@takulog.info'), esc_html('llms-txt@takulog.info')) . '</li>';
                /* translators: %1$s: Homepage URL for href, %2$s: Homepage URL for display */
                echo '<li>' . sprintf(__('ホームページ: <a href="%1$s" target="_blank">%2$s</a>', 'wp-llms-txt-generator'), esc_url('https://mobile-cheap.jp'), esc_html('https://mobile-cheap.jp')) . '</li>';
                ?>
            </ul>
        </div>

        <div class="card">
            <h3><?php esc_html_e('使い方', 'wp-llms-txt-generator'); ?></h3>
            <ol>
                <li><?php esc_html_e('「設定」タブで、出力したい投稿タイプを選択します。', 'wp-llms-txt-generator'); ?></li>
                <li><?php esc_html_e('必要に応じて、カスタムヘッダーテキストや抜粋の含め方を設定します。', 'wp-llms-txt-generator'); ?></li>
                <li><?php esc_html_e('「生成」タブで「WP LLMS TXT Generator ファイルを生成」ボタンをクリックします。', 'wp-llms-txt-generator'); ?></li>
                <li><?php esc_html_e('生成されたファイルのURLが表示されます。これらのURLをLLMの学習データとして使用できます。', 'wp-llms-txt-generator'); ?></li>
            </ol>

            <h4><?php esc_html_e('自動更新について', 'wp-llms-txt-generator'); ?></h4>
            <p><?php esc_html_e('「投稿の変更時に自動更新」を有効にすると、投稿の追加・更新・削除時に自動的にファイルが更新されます。', 'wp-llms-txt-generator'); ?></p>
            <p><?php esc_html_e('「定期的に自動生成」を有効にすると、指定した頻度（毎時、1日2回、毎日、毎週）でファイルが自動的に生成されます。', 'wp-llms-txt-generator'); ?></p>

            <h4><?php esc_html_e('URLフィルタリングについて', 'wp-llms-txt-generator'); ?></h4>
            <p><?php esc_html_e('「含めるURL」と「除外するURL」を設定することで、特定のURLのみを出力したり、特定のURLを除外したりすることができます。', 'wp-llms-txt-generator'); ?></p>
            <p><?php esc_html_e('ワイルドカード（*）を使用して、パターンマッチングができます。例：', 'wp-llms-txt-generator'); ?></p>
            <ul>
                <li><code>https://example.com/category/*</code> - category以下のすべてのURL</li>
                <li><code>*/2023/*</code> - URLに2023を含むすべてのURL</li>
            </ul>
        </div>

        <div class="card">
            <h3><?php esc_html_e('トラブルシューティング', 'wp-llms-txt-generator'); ?></h3>

            <p><?php esc_html_e('URLが正しく除外されない場合は、以下の点を確認してください：', 'wp-llms-txt-generator'); ?></p>
            <ol>
                <li><?php esc_html_e('デバッグモードを有効にして、URL処理のログを確認', 'wp-llms-txt-generator'); ?></li>
                <li><?php esc_html_e('URLの形式が正しいか（絶対URLと相対URL）', 'wp-llms-txt-generator'); ?></li>
                <li><?php esc_html_e('ワイルドカードの使用方法が適切か', 'wp-llms-txt-generator'); ?></li>
            </ol>

            <div class="troubleshooting-form">
                <h4><?php esc_html_e('デバッグ設定', 'wp-llms-txt-generator'); ?></h4>
                <form method="post" action="options.php">
                    <?php settings_fields('llms_txt_generator_settings'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('デバッグモード', 'wp-llms-txt-generator'); ?></th>
                            <td>
                                <input type="checkbox" name="llms_txt_generator_debug_mode" value="1" <?php checked(1, get_option('llms_txt_generator_debug_mode', false), true); ?> />
                                <p class="description"><?php esc_html_e('有効にすると、URLフィルタリングに関する詳細なログが生成されます。問題が発生した場合に役立ちます。', 'wp-llms-txt-generator'); ?></p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(__('デバッグ設定を保存', 'wp-llms-txt-generator')); ?>
                </form>
            </div>

            <p><?php esc_html_e('デバッグログは以下の場所に保存されます：', 'wp-llms-txt-generator'); ?></p>
            <div class="debug-log-path">wp-content/plugins/wp-llms-txt-generator/logs/url_debug.log</div>
        </div>

        <div class="card">
            <h3><?php esc_html_e('高度な設定', 'wp-llms-txt-generator'); ?></h3>

            <div class="advanced-setting">
                <h4><?php esc_html_e('アンインストール時の設定保持', 'wp-llms-txt-generator'); ?></h4>
                <form method="post" action="options.php">
                    <?php settings_fields('llms_txt_generator_uninstall_settings'); ?>
                    <p>
                        <input type="checkbox" name="llms_txt_generator_keep_settings" value="1" <?php checked(1, get_option('llms_txt_generator_keep_settings', false), true); ?> />
                        <?php esc_html_e('プラグインをアンインストールしても設定を保持する', 'wp-llms-txt-generator'); ?>
                    </p>
                    <p class="description"><?php esc_html_e('有効にすると、プラグインをアンインストールしても設定が削除されません。再インストール時に以前の設定を引き継ぐことができます。', 'wp-llms-txt-generator'); ?></p>
                    <?php submit_button(__('設定を保存', 'wp-llms-txt-generator')); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.tab-content').hide();
    $('#settings-tab').show();

    $('.nav-tab').click(function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });

    var hash = window.location.hash;
    if (hash) {
        $('.nav-tab[href="' + hash + '"]').click();
    }

    function toggleScheduleFrequency() {
        if ($('input[name="llms_txt_generator_schedule_enabled"]').is(':checked')) {
            $('#schedule-frequency-row').show();
        } else {
            $('#schedule-frequency-row').hide();
        }
    }

    toggleScheduleFrequency();
    $('input[name="llms_txt_generator_schedule_enabled"]').change(toggleScheduleFrequency);
});
</script>

<style>
.card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 15px;
}
.file-info {
    background: #f8f9fa;
    border-left: 4px solid #007cba;
    margin: 10px 0;
    padding: 10px;
}
.debug-log-path {
    background: #f0f0f1;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    color: #3c434a;
    font-family: monospace;
    margin: 10px 0;
    padding: 10px;
}
</style>
<?php
}
