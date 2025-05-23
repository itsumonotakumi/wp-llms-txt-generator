=== LLMS TXT and Full TXT Generator ===
Contributors: itsumonotakumi, rankth
Tags: llm, ai, txt, content export, large language model
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 2.0
Requires PHP: 7.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPressサイト内のコンテンツをLLM（大規模言語モデル）の学習データとして利用できるllms.txtとllms-full.txtファイルに出力します。

== Description ==

このプラグインは、サイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力します。生成されたファイルはLLM（大規模言語モデル）の学習データとして利用できます。

「LLMs-Full.txt and LLMs.txt Generator」は[WordPress.orgで公開されているLLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/)をフォークして機能拡張したものです。元の開発者[rankth](https://profiles.wordpress.org/rankth/)の貢献に感謝します。

= 多言語対応 =

* 日本語（デフォルト）
* 英語（English）

= 免責事項 =

**このプログラムによるいかなる不都合や事故も補償しません。ご使用は自己責任でお願いします。**

= 主な機能 =

* 指定した投稿タイプのコンテンツを構造化されたテキストファイルに変換
* llms.txt（URLリスト）とllms-full.txt（コンテンツ全文）の2種類のファイルを生成
* **新機能**: 投稿の追加・更新・削除時に自動的にファイルを更新
* **新機能**: WordPress Cronによる定期的な自動生成（毎時/1日2回/毎日/毎週）
* **改善**: 強化されたURLパターンフィルタリング（末尾のスラッシュ対応など）
* **新機能**: カスタムヘッダーテキストの追加
* **新機能**: デバッグモードによるURL処理の詳細ログ
* **改善**: 使いやすいタブベースの管理インターフェース

= URLフィルターの使い方 =

URLフィルターでは、ワイルドカード（*）を使用してパターンを指定できます：

* `/blog/*` - blogディレクトリ内のすべてのページを対象にします
* `*/2023/*` - 2023を含むURLすべてを対象にします

**注意**: URLの末尾のスラッシュは自動的に削除されるため、`/contact/` と `/contact` は同じものとして扱われます。

= 連絡先情報 =

* メールアドレス: llms-txt@takulog.info
* ホームページ: https://mobile-cheap.jp
* X (Twitter): https://x.com/itsumonotakumi
* Threads: https://www.threads.net/@itsumonotakumi
* YouTube: https://www.youtube.com/@itsumonotakumi

== Installation ==

1. プラグインをアップロードするか、WordPress管理画面からプラグインを検索してインストール
2. プラグインを有効化
3. 「設定」→「LLMS.txt生成設定」から設定を行う
4. ファイルに含める投稿タイプを選択し、必要に応じて他の設定を行う
5. 「生成」タブで「LLMS.txtファイルを生成」ボタンをクリックしてファイルを生成

== Screenshots ==

1. 設定画面 - さまざまな投稿タイプの選択、カスタムヘッダー、URLフィルター、デバッグモードなどの設定ができます
2. 生成画面 - ファイルの生成状況やステータスを確認できます

== Frequently Asked Questions ==

= llms.txtとllms-full.txtの違いは何ですか？ =

llms.txtはURLと投稿タイトルのリストのみを含み、llms-full.txtは投稿の全文を含みます。

= URLが正しく除外されない場合はどうすればいいですか？ =

デバッグモードを有効にして、URL処理のログを確認してください。ログは `wp-content/plugins/wp-llms-txt-generator/logs/url_debug.log` に保存されます。

= 自動更新と定期実行の違いは何ですか？ =

自動更新は投稿の追加・更新・削除時にファイルを更新し、定期実行は設定した頻度（毎時/1日2回/毎日/毎週）で自動的にファイルを生成します。

== Changelog ==

= 2.0 =
* Plugin Checkの警告をすべて解消: error_log()呼び出しを完全に削除
* 適切なデバッグログシステムの実装: デバッグモードが有効な場合のみログを出力
* date()関数をgmdate()に置き換えてタイムゾーン問題を解決
* ドキュメントの更新とライセンス表記の明確化
* 元のプラグインバージョンへの明示的な参照を追加

= 1.9.6 =
* Plugin Checkの警告を解消: error_log()呼び出しをカスタムデバッグ関数に置き換え
* uninstall.phpのerror_log()呼び出しを修正
* コード品質の向上: デバッグログ処理の一貫性を改善

= 1.9.5 =
* コード品質の向上: error_logをデバッグモード時のみ実行するよう改善（重複条件の削除）
* セキュリティの強化: 出力の適切なエスケープとサニタイズの実装

= 1.9.4 =
* 国際化の改善: 翻訳者向けコメントの追加とプレースホルダーの順番指定
* セキュリティの強化: 出力の適切なエスケープとサニタイズの実装
* コード品質の向上: error_logをデバッグモード時のみ実行するよう改善
* WordPress標準に準拠するため.gitignoreをgitignore.txtにリネーム

= 1.9.3 =
* 日本語テキストの文字化けを修正するためにUTF-8エンコーディングを実装
* ファイル書き込み時にUTF-8 BOMを追加して文字コードを明示
* mbstring拡張モジュールの依存関係チェックを追加

= 1.9.2 =
* 多言語対応を追加（日本語・英語）
* readme.txtの改善とスクリーンショットの追加
* UI/UXの細かな調整
* 内部処理の最適化

= 1.9.1 =
* 投稿の追加・更新・削除時に自動的にファイルを更新する機能を追加
* WordPress Cronを使った定期的なファイル生成機能を追加
* ファイルの先頭に任意のテキストを追加できるカスタムヘッダーテキスト機能を追加
* URLフィルタリング処理のデバッグモードとログ機能を追加
* タブ式インターフェースによるナビゲーション改善
* URLの除外機能が正常に動作しない問題を修正
* 末尾のスラッシュを含むURLの正規化処理を追加
* 投稿タイプごとの見出しが正しく表示されない問題を修正

= 1.9.1.1 =
* 除外・包含ルールの登録時にも末尾のスラッシュを削除する処理を追加
* ヘルプ内容を更新し、末尾のスラッシュ処理に関する説明を追加

= 1.9 =
* オリジナルバージョン by rankth

== Upgrade Notice ==

= 2.0 =
メジャーバージョンアップデート: Plugin Checkの警告をすべて解消し、コード品質とセキュリティを向上させました。date()関数をgmdate()に置き換え、適切なデバッグログシステムを実装しました。

= 1.9.6 =
この更新ではPlugin Checkツールで検出されたerror_log()に関する警告を解消し、デバッグログ処理の一貫性を向上させています。

= 1.9.5 =
この更新ではコード品質の向上とセキュリティの強化が行われています。Plugin Checkツールで検出された問題が修正されています。

= 1.9.4 =
この更新では国際化の改善、セキュリティの強化、コード品質の向上が行われています。Plugin Checkツールで検出された問題が修正されています。

= 1.9.3 =
この更新では日本語テキストの文字化け問題が修正され、UTF-8エンコーディングが正しく実装されています。

= 1.9.2 =
この更新では多言語対応（日本語・英語）が追加され、UIの改善が行われています。

= 1.9.1 =
この更新では、自動更新機能やURL除外処理の改善など、多くの機能強化が行われています。URLフィルタリングに問題があった場合は、この更新で解決される可能性があります。

== フィードバック & サポート ==

このプラグインは[GitHub](https://github.com/itsumonotakumi/wp-llms-txt-generator)でオープンソースとして開発されています。バグ報告や機能リクエストはメールにてお問い合わせください。
