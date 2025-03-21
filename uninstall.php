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
);

foreach ( $options as $option ) {
	delete_option( $option );
}

// スケジュールイベントの削除
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

		// 各サイトのオプションを削除
		foreach ( $options as $option ) {
			delete_option( $option );
		}

		// 各サイトのスケジュールを削除
		$timestamp = wp_next_scheduled( 'llms_txt_generate_schedule' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'llms_txt_generate_schedule' );
		}

		// 各サイトのトランジェントを削除
		delete_transient( 'llms_files_updating' );

		restore_current_blog();
	}
}
