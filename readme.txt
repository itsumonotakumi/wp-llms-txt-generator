=== LLMS TXT and Full TXT Generator ===
Contributors: itsumonotakumi, rankth
Tags: llm, ai, txt, content export, large language model
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.9.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPressサイト内のコンテンツをLLM（大規模言語モデル）の学習データとして利用できるllms.txtとllms-full.txtファイルに出力します。

== Description ==

このプラグインは、サイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力します。生成されたファイルはLLM（大規模言語モデル）の学習データとして利用できます。

「LLMs-Full.txt and LLMs.txt Generator」は[WordPress.orgで公開されているLLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/)をフォークして機能拡張したものです。元の開発者[rankth](https://profiles.wordpress.org/rankth/)の貢献に感謝します。

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

== Frequently Asked Questions ==

= llms.txtとllms-full.txtの違いは何ですか？ =

llms.txtはURLと投稿タイトルのリストのみを含み、llms-full.txtは投稿の全文を含みます。

= URLが正しく除外されない場合はどうすればいいですか？ =

デバッグモードを有効にして、URL処理のログを確認してください。ログは `wp-content/plugins/llms-txt-full-txt-generator/logs/url_debug.log` に保存されます。

= 自動更新と定期実行の違いは何ですか？ =

自動更新は投稿の追加・更新・削除時にファイルを更新し、定期実行は設定した頻度（毎時/1日2回/毎日/毎週）で自動的にファイルを生成します。

== Screenshots ==

1. 設定画面
2. 生成画面
3. URLフィルターの設定例
4. デバッグモードのログ例

== Changelog ==

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

= 1.9.1 =
この更新では、自動更新機能やURL除外処理の改善など、多くの機能強化が行われています。URLフィルタリングに問題があった場合は、この更新で解決される可能性があります。

== フィードバック & サポート ==

このプラグインは[GitHub](https://github.com/itsumonotakumi/llms-txt-full-txt-generator)でオープンソースとして開発されています。バグ報告や機能リクエストはメールにてお問い合わせください。
