<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// 管理画面でない場合は処理しない
if (!is_admin()) {
    return;
}

// 必要なWordPress管理画面関数を確認
if (!function_exists('do_settings_sections')) {
    require_once(ABSPATH . 'wp-admin/includes/template.php');
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="nav-tab-wrapper">
        <a href="#settings-tab" id="settings-tab-link" class="nav-tab nav-tab-active"><?php esc_html_e('設定', 'llms-txt-full-txt-generator'); ?></a>
        <a href="#generate-tab" id="generate-tab-link" class="nav-tab"><?php esc_html_e('生成', 'llms-txt-full-txt-generator'); ?></a>
        <a href="#help-tab" id="help-tab-link" class="nav-tab"><?php esc_html_e('ヘルプ', 'llms-txt-full-txt-generator'); ?></a>
    </div>

    <div id="settings-tab" class="tab-content">
        <form method="post" action="options.php">
            <?php
            settings_fields('llms_txt_generator_settings');
            ?>

            <h2><?php esc_html_e('基本設定', 'llms-txt-full-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('投稿タイプを選択', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <?php
                        $post_types = get_post_types(array('public' => true), 'objects');
                        $selected_post_types = get_option('llms_txt_generator_post_types', array());
                        foreach ($post_types as $post_type) {
                            $checked = in_array($post_type->name, $selected_post_types) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="llms_txt_generator_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
                        }
                        ?>
                        <p class="description"><?php esc_html_e('生成するファイルに含める投稿タイプを選択してください。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('カスタムヘッダーテキスト', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_custom_header" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_custom_header')); ?></textarea>
                        <p class="description"><?php esc_html_e('このテキストはllms.txtとllms-full.txtファイルのURLリストの上に表示されます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('抜粋を含める', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_include_excerpt" value="1" <?php checked(1, get_option('llms_txt_generator_include_excerpt'), true); ?> />
                        <p class="description"><?php esc_html_e('llms-full.txtファイルに投稿の抜粋を含めます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('更新設定', 'llms-txt-full-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('投稿の変更時に自動更新', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_auto_update" value="1" <?php checked(1, get_option('llms_txt_generator_auto_update', true), true); ?> />
                        <p class="description"><?php esc_html_e('有効にすると、投稿の追加・更新・削除時に自動的にLLMS.txtファイルが更新されます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('デバッグモード', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_debug_mode" value="1" <?php checked(1, get_option('llms_txt_generator_debug_mode', false), true); ?> />
                        <p class="description"><?php esc_html_e('有効にすると、URLフィルタリングに関する詳細なログが生成されます。問題が発生した場合に役立ちます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('定期的に自動生成', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <input type="checkbox" name="llms_txt_generator_schedule_enabled" value="1" <?php checked(1, get_option('llms_txt_generator_schedule_enabled', false), true); ?> />
                        <p class="description"><?php esc_html_e('有効にすると、指定した頻度でLLMS.txtファイルが自動的に生成されます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top" id="schedule-frequency-row">
                    <th scope="row"><?php esc_html_e('自動生成の頻度', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <select name="llms_txt_generator_schedule_frequency">
                            <?php
                            $frequency = get_option('llms_txt_generator_schedule_frequency', 'daily');
                            $schedules = array(
                                'hourly' => __('毎時', 'llms-txt-full-txt-generator'),
                                'twicedaily' => __('1日2回', 'llms-txt-full-txt-generator'),
                                'daily' => __('毎日', 'llms-txt-full-txt-generator'),
                                'weekly' => __('毎週', 'llms-txt-full-txt-generator')
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
                        <p class="description"><?php esc_html_e('LLMSファイルを自動生成する頻度を選択してください。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('フィルター設定', 'llms-txt-full-txt-generator'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('含めるURL', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_include_urls" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_include_urls')); ?></textarea>
                        <p class="description"><?php esc_html_e('含めるURLを1行に1つずつ入力してください。*をワイルドカードとして使用できます。空の場合はすべてのURLが含まれます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('除外するURL', 'llms-txt-full-txt-generator'); ?></th>
                    <td>
                        <textarea name="llms_txt_generator_exclude_urls" rows="5" cols="50"><?php echo esc_textarea(get_option('llms_txt_generator_exclude_urls')); ?></textarea>
                        <p class="description"><?php esc_html_e('除外するURLを1行に1つずつ入力してください。*をワイルドカードとして使用できます。', 'llms-txt-full-txt-generator'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('設定を保存', 'llms-txt-full-txt-generator')); ?>
        </form>
    </div>

    <div id="generate-tab" class="tab-content">
        <h2><?php esc_html_e('LLMS.txtファイルを生成', 'llms-txt-full-txt-generator'); ?></h2>
        <p><?php esc_html_e('下のボタンをクリックすると、現在の設定に基づいてllms.txtとllms-full.txtファイルを生成します。', 'llms-txt-full-txt-generator'); ?></p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('llms_generate_action', 'llms_nonce'); ?>
            <input type="hidden" name="action" value="generate_llms_txt">
            <input type="submit" name="generate_llms_txt" class="button button-primary" value="<?php echo esc_attr__('LLMS.txtファイルを生成', 'llms-txt-full-txt-generator'); ?>">
        </form>

        <div class="llms-file-status" style="margin-top: 20px;">
            <h3><?php esc_html_e('ファイルステータス', 'llms-txt-full-txt-generator'); ?></h3>
            <?php
            $root_dir = ABSPATH;
            $llms_txt_path = $root_dir . '/llms.txt';
            $llms_full_txt_path = $root_dir . '/llms-full.txt';

            if (file_exists($llms_txt_path)) {
                $llms_txt_url = home_url('/llms.txt');
                $modified = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), filemtime($llms_txt_path));
                echo '<div class="file-info">';
                /* translators: %s: URL to the llms.txt file */
                echo '<p>' . sprintf(esc_html__('LLMS.txtファイル: %s', 'llms-txt-full-txt-generator'), '<a href="' . esc_url($llms_txt_url) . '" target="_blank">' . esc_html($llms_txt_url) . '</a>') . '</p>';
                /* translators: %s: Last modified date and time of the file */
                echo '<p>' . sprintf(esc_html__('最終更新日時: %s', 'llms-txt-full-txt-generator'), esc_html($modified)) . '</p>';
                /* translators: %s: File size in human readable format */
                echo '<p>' . sprintf(esc_html__('ファイルサイズ: %s', 'llms-txt-full-txt-generator'), esc_html(size_format(filesize($llms_txt_path)))) . '</p>';
                echo '</div>';
            } else {
                echo '<p>' . esc_html__('LLMS.txtファイルはまだ生成されていません。', 'llms-txt-full-txt-generator') . '</p>';
            }

            if (file_exists($llms_full_txt_path)) {
                $llms_full_txt_url = home_url('/llms-full.txt');
                $modified = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), filemtime($llms_full_txt_path));
                echo '<div class="file-info">';
                /* translators: %s: URL to the llms-full.txt file */
                echo '<p>' . sprintf(esc_html__('LLMS-Full.txtファイル: %s', 'llms-txt-full-txt-generator'), '<a href="' . esc_url($llms_full_txt_url) . '" target="_blank">' . esc_html($llms_full_txt_url) . '</a>') . '</p>';
                /* translators: %s: Last modified date and time of the file */
                echo '<p>' . sprintf(esc_html__('最終更新日時: %s', 'llms-txt-full-txt-generator'), esc_html($modified)) . '</p>';
                /* translators: %s: File size in human readable format */
                echo '<p>' . sprintf(esc_html__('ファイルサイズ: %s', 'llms-txt-full-txt-generator'), esc_html(size_format(filesize($llms_full_txt_path)))) . '</p>';
                echo '</div>';
            } else {
                echo '<p>' . esc_html__('LLMS-Full.txtファイルはまだ生成されていません。', 'llms-txt-full-txt-generator') . '</p>';
            }
            ?>
        </div>
    </div>

    <div id="help-tab" class="tab-content">
        <h2><?php esc_html_e('ヘルプとサポート', 'llms-txt-full-txt-generator'); ?></h2>

        <div class="card">
            <h3><?php esc_html_e('このプラグインについて', 'llms-txt-full-txt-generator'); ?></h3>
            <p><?php esc_html_e('このプラグインはサイト内のコンテンツをllms.txtとllms-full.txtファイルに出力します。LLMの学習データとして利用できます。', 'llms-txt-full-txt-generator'); ?></p>
            <p><?php esc_html_e('このプラグインは元々、rankthによって開発されたLLMs-Full.txt and LLMs.txt Generatorをベースに、いつもの匠によって機能拡張されたものです。', 'llms-txt-full-txt-generator'); ?></p>
            <?php
            /* translators: %s: URL to the GitHub repository */
            echo '<p>' . sprintf(esc_html__('ソースコードは<a href="%s" target="_blank">GitHub</a>で公開されています。', 'llms-txt-full-txt-generator'), esc_url('https://github.com/itsumonotakumi/llms-txt-full-txt-generator')) . '</p>';
            /* translators: %s: URL to the original plugin on WordPress.org */
            echo '<p>' . sprintf(esc_html__('元のプラグイン: <a href="%s" target="_blank">LLMs-Full.txt and LLMs.txt Generator</a>', 'llms-txt-full-txt-generator'), esc_url('https://wordpress.org/plugins/llms-full-txt-generator/')) . '</p>';
            ?>

            <h4><?php esc_html_e('免責事項', 'llms-txt-full-txt-generator'); ?></h4>
            <p class="notice notice-warning" style="padding: 10px; margin: 10px 0;"><?php esc_html_e('このプログラムによるいかなる不都合や事故も補償しません。ご使用は自己責任でお願いします。', 'llms-txt-full-txt-generator'); ?></p>

            <h4><?php esc_html_e('連絡先情報', 'llms-txt-full-txt-generator'); ?></h4>
            <ul>
                <?php
                /* translators: %1$s: Email address for mailto link, %2$s: Email address for display */
                echo '<li>' . sprintf(esc_html__('メールアドレス: <a href="mailto:%1$s">%2$s</a>', 'llms-txt-full-txt-generator'), esc_attr('llms-txt@takulog.info'), esc_html('llms-txt@takulog.info')) . '</li>';
                /* translators: %1$s: Homepage URL for href, %2$s: Homepage URL for display */
                echo '<li>' . sprintf(esc_html__('ホームページ: <a href="%1$s" target="_blank">%2$s</a>', 'llms-txt-full-txt-generator'), esc_url('https://mobile-cheap.jp'), esc_html('https://mobile-cheap.jp')) . '</li>';
                /* translators: %s: X (Twitter) profile URL */
                echo '<li>' . sprintf(esc_html__('X (Twitter): <a href="%1$s" target="_blank">@itsumonotakumi</a>', 'llms-txt-full-txt-generator'), esc_url('https://x.com/itsumonotakumi')) . '</li>';
                /* translators: %s: Threads profile URL */
                echo '<li>' . sprintf(esc_html__('Threads: <a href="%1$s" target="_blank">@itsumonotakumi</a>', 'llms-txt-full-txt-generator'), esc_url('https://www.threads.net/@itsumonotakumi')) . '</li>';
                /* translators: %s: YouTube channel URL */
                echo '<li>' . sprintf(esc_html__('YouTube: <a href="%1$s" target="_blank">@itsumonotakumi</a>', 'llms-txt-full-txt-generator'), esc_url('https://www.youtube.com/@itsumonotakumi')) . '</li>';
                ?>
            </ul>
        </div>

        <div class="card">
            <h3><?php esc_html_e('プラグインの使い方', 'llms-txt-full-txt-generator'); ?></h3>
            <p><?php esc_html_e('このプラグインは、WordPressサイトのコンテンツをAIの学習データとして利用するためのllms.txtとllms-full.txtファイルを生成します。', 'llms-txt-full-txt-generator'); ?></p>
            <ol>
                <li><?php esc_html_e('設定タブで、ファイルに含める投稿タイプを選択します。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('必要に応じて、カスタムヘッダーテキストを追加します。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「抜粋を含める」オプションを設定します（llms-full.txtに投稿の抜粋を含めるかどうか）。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「投稿の変更時に自動更新」を有効にすると、コンテンツ更新時に自動的にファイルが生成されます。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「定期的に自動生成」を有効にして頻度を設定すると、指定した間隔で自動的にファイルが生成されます。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「デバッグモード」を有効にすると、URL処理の詳細なログが生成されます。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「含めるURL」に特定のパターンを指定すると、そのパターンに一致するURLのみが含まれます（空の場合はすべて含まれます）。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('「除外するURL」に特定のパターンを指定すると、そのパターンに一致するURLは除外されます。', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('設定を保存した後、「生成」タブで「LLMS.txtファイルを生成」ボタンをクリックします。', 'llms-txt-full-txt-generator'); ?></li>
            </ol>

            <h4><?php esc_html_e('URLフィルターの使い方', 'llms-txt-full-txt-generator'); ?></h4>
            <p><?php esc_html_e('URLフィルターでは、ワイルドカード（*）を使用してパターンを指定できます。例：', 'llms-txt-full-txt-generator'); ?></p>
            <ul>
                <li><code>/blog/*</code> - <?php esc_html_e('blogディレクトリ内のすべてのページを対象にします', 'llms-txt-full-txt-generator'); ?></li>
                <li><code>*/2023/*</code> - <?php esc_html_e('2023を含むURLすべてを対象にします', 'llms-txt-full-txt-generator'); ?></li>
            </ul>
            <p><?php esc_html_e('入力例：', 'llms-txt-full-txt-generator'); ?></p>
            <pre>https://example.com/page1
https://example.com/page2
/contact
/about-us
*/exclude-this-part/*</pre>
            <p class="description"><?php esc_html_e('注意: URLの末尾のスラッシュは自動的に削除されるため、/contact/ と /contact は同じものとして扱われます。', 'llms-txt-full-txt-generator'); ?></p>
        </div>

        <div class="card">
            <h3><?php esc_html_e('トラブルシューティング', 'llms-txt-full-txt-generator'); ?></h3>
            <p><?php esc_html_e('URLが正しく除外されない場合は、以下の点を確認してください：', 'llms-txt-full-txt-generator'); ?></p>
            <ol>
                <li><?php esc_html_e('デバッグモードを有効にして、URL処理のログを確認', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('URLの形式が正しいか（絶対URLと相対URL）', 'llms-txt-full-txt-generator'); ?></li>
                <li><?php esc_html_e('ワイルドカードの使用方法が適切か', 'llms-txt-full-txt-generator'); ?></li>
            </ol>
            <p><?php esc_html_e('デバッグログは以下の場所に保存されます：', 'llms-txt-full-txt-generator'); ?></p>
            <code>wp-content/plugins/llms-txt-full-txt-generator/logs/url_debug.log</code>
        </div>

        <div class="card">
            <h3><?php esc_html_e('高度な設定', 'llms-txt-full-txt-generator'); ?></h3>

            <div class="advanced-setting">
                <h4><?php esc_html_e('アンインストール時の設定保持', 'llms-txt-full-txt-generator'); ?></h4>
                <form method="post" action="options.php">
                    <?php settings_fields('llms_txt_generator_uninstall_settings'); ?>
                    <p>
                        <input type="checkbox" name="llms_txt_generator_keep_settings" value="1" <?php checked(1, get_option('llms_txt_generator_keep_settings', false), true); ?> />
                        <?php esc_html_e('プラグインをアンインストールしても設定を保持する', 'llms-txt-full-txt-generator'); ?>
                    </p>
                    <p class="description"><?php esc_html_e('有効にすると、プラグインをアンインストールしても設定が削除されません。再インストール時に以前の設定を引き継ぐことができます。', 'llms-txt-full-txt-generator'); ?></p>
                    <?php submit_button(__('設定を保存', 'llms-txt-full-txt-generator')); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 2px;
    margin-top: 20px;
    padding: 20px;
    position: relative;
    box-shadow: 0 1px 1px rgba(0,0,0,0.04);
}

.advanced-setting {
    margin-bottom: 20px;
}

.file-info {
    background: #f8f9fa;
    border-left: 4px solid #2271b1;
    padding: 10px;
    margin-bottom: 15px;
}

.tab-content {
    display: none;
    margin-top: 20px;
}

#settings-tab {
    display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.tab-content').hide();

    $('#settings-tab').show();

    function initTabs() {
        if (window.location.hash && $(window.location.hash).length) {
            var hash = window.location.hash;
            $('.nav-tab').removeClass('nav-tab-active');
            $('a[href="' + hash + '"]').addClass('nav-tab-active');
            $('.tab-content').hide();
            $(hash).show();
        } else {
            $('#settings-tab-link').addClass('nav-tab-active');
            $('#settings-tab').show();
        }
    }

    initTabs();

    $('.nav-tab').on('click', function(e) {
        e.preventDefault();

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        var targetTab = $(this).attr('href');
        $('.tab-content').hide();
        $(targetTab).show();

        if (history.replaceState) {
            history.replaceState(null, null, targetTab);
        }
    });

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
