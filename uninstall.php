<?php
/**
 * LLMS TXT and Full TXT Generator Uninstall
 *
 * プラグインのアンインストール時にクリーンアップを行うファイル
 *
 * @package LLMS_TXT_Generator
 */

// WordPress環境外からの実行を防止
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// 設定を完全に削除するかどうかのオプション
// デフォルトではfalseにして、設定を保持するようにします
$delete_all_options = get_option('llms_txt_generator_delete_all_data', false);

if ($delete_all_options) {
	// プラグインのオプションを削除
	$options = array(
		'llms_txt_generator_post_types',
		'llms_txt_generator_include_excerpt',
		'llms_txt_generator_auto_update',
		'llms_txt_generator_debug_mode',
		'llms_txt_generator_schedule_enabled',
		'llms_txt_generator_schedule_frequency',
		'llms_txt_generator_custom_header',
		'llms_txt_generator_include_urls',
		'llms_txt_generator_exclude_urls',
		'llms_txt_generator_delete_all_data', // 自分自身も削除
	);

	foreach ( $options as $option ) {
		delete_option( $option );
	}
} else {
	// バージョン情報だけ残して他は消さない
	// バージョン情報があると次回のインストール時に設定を引き継ぐことができる
	if (get_option('llms_txt_generator_debug_mode', false)) {
		// デバッグモードが有効な場合のみログを記録（error_log()を使用しない）
		$upload_dir = wp_upload_dir();
		$log_dir = trailingslashit($upload_dir['basedir']) . 'llms-txt-generator-logs';
		
		// ログディレクトリが存在しない場合は作成
		if (!file_exists($log_dir)) {
			wp_mkdir_p($log_dir);
			file_put_contents(trailingslashit($log_dir) . '.htaccess', "Order deny,allow\nDeny from all");
		}
		
		$log_file = trailingslashit($log_dir) . 'debug.log';
		$timestamp = date('Y-m-d H:i:s');
		$message = 'LLMS TXT Generator: アンインストール時に設定を保持しました。';
		
		file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
	}
}

// スケジュールイベントの削除（設定保持に関わらず実行）
$timestamp = wp_next_scheduled( 'llms_txt_generate_schedule' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'llms_txt_generate_schedule' );
}

/**
 * llms.txtとllms-full.txtファイルは削除しない
 * これらは重要なデータであり、プラグインの削除時に消失すると問題が発生する可能性があります。
 * ユーザーが明示的にファイルを削除したい場合は、手動で行う必要があります。
 */

// トランジェントの削除
delete_transient( 'llms_files_updating' );

// マルチサイト対応（オプション）
if ( is_multisite() ) {
	$sites = get_sites();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );

		// 各サイトの設定を完全に削除するかどうかのオプションを取得
		$site_delete_all_options = get_option('llms_txt_generator_delete_all_data', false);

		if ($site_delete_all_options) {
			// 各サイトのオプションを削除
			$options = array(
				'llms_txt_generator_post_types',
				'llms_txt_generator_include_excerpt',
				'llms_txt_generator_auto_update',
				'llms_txt_generator_debug_mode',
				'llms_txt_generator_schedule_enabled',
				'llms_txt_generator_schedule_frequency',
				'llms_txt_generator_custom_header',
				'llms_txt_generator_include_urls',
				'llms_txt_generator_exclude_urls',
				'llms_txt_generator_delete_all_data', // 自分自身も削除
			);

			foreach ( $options as $option ) {
				delete_option( $option );
			}
		}

		// 各サイトのスケジュールを削除（設定保持に関わらず実行）
		$timestamp = wp_next_scheduled( 'llms_txt_generate_schedule' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'llms_txt_generate_schedule' );
		}

		// 各サイトのトランジェントを削除
		delete_transient( 'llms_files_updating' );

		restore_current_blog();
	}
}
