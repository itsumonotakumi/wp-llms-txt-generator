# LLMS TXT and Full TXT Generator

![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-purple)
![License](https://img.shields.io/badge/License-GPL%20v2-green)
![Version](https://img.shields.io/badge/Version-2.0-orange)

WordPressサイト内の投稿やページを自動的にllms.txtとllms-full.txtファイルに出力するプラグインです。LLM（大規模言語モデル）の学習データとして利用できます。

> このプラグインは[LLMs-Full.txt and LLMs.txt Generator](https://wordpress.org/plugins/llms-full-txt-generator/)をフォークして機能拡張したものです。元の開発者[rankth](https://profiles.wordpress.org/rankth/)の貢献に感謝します。


## 概要

このプラグインは、LLM用のllms.txtとllms-full.txtファイルをWordPressサイトのルートディレクトリに生成します。これにより、AIモデルがWebサイトのコンテンツを効率的に理解し、適切に参照できるようになります。

オリジナルの機能に加えて、自動更新機能やURLフィルタリングの改善など、多くの機能強化を行っています。

## 免責事項

**このプログラムによるいかなる不都合や事故も補償しません。ご使用は自己責任でお願いします。**

## 主な機能

- 指定した投稿タイプのコンテンツを構造化されたテキストファイルに変換
- llms.txt（URLリスト）とllms-full.txt（コンテンツ全文）の2種類のファイルを生成
- **新機能**: 投稿の追加・更新・削除時に自動的にファイルを更新
- **新機能**: WordPress Cronによる定期的な自動生成（毎時/1日2回/毎日/毎週）
- **改善**: 強化されたURLパターンフィルタリング（末尾のスラッシュ対応など）
- **新機能**: カスタムヘッダーテキストの追加
- **新機能**: デバッグモードによるURL処理の詳細ログ
- **改善**: 使いやすいタブベースの管理インターフェース
- **新機能**: 多言語対応（日本語・英語）

## インストール方法

1. [Releases](https://github.com/itsumonotakumi/wp-llms-txt-generator/releases)から最新のZIPファイルをダウンロード
2. WordPressの管理画面から「プラグイン」→「新規追加」→「プラグインのアップロード」を選択
3. ダウンロードしたZIPファイルをアップロードしてインストール
4. プラグインを有効化
5. 「設定」→「LLMS.txt生成設定」から設定を行う

または、`wp-content/plugins/` ディレクトリに直接ファイルを展開することもできます。

## 使い方

### 基本設定

1. 「設定」→「LLMS.txt生成設定」を開く
2. 「基本設定」タブで、ファイルに含める投稿タイプを選択
3. 必要に応じてカスタムヘッダーテキストを追加
4. 「抜粋を含める」オプションを設定（llms-full.txtに投稿の抜粋を含めるかどうか）

### 更新設定

1. 「投稿の変更時に自動更新」を有効にすると、コンテンツ更新時に自動的にファイルが生成されます
2. 「定期的に自動生成」を有効にして頻度を設定すると、指定した間隔で自動的にファイルが生成されます
3. 「デバッグモード」を有効にすると、URL処理の詳細なログが生成されます

### フィルター設定

1. 「含めるURL」に特定のパターンを指定すると、そのパターンに一致するURLのみが含まれます（空の場合はすべて含まれます）
2. 「除外するURL」に特定のパターンを指定すると、そのパターンに一致するURLは除外されます

### ファイル生成

「生成」タブで「LLMS.txtファイルを生成」ボタンをクリックすると、設定に基づいてファイルが生成されます。

## URLフィルターの使い方

URLフィルターでは、ワイルドカード（*）を使用してパターンを指定できます。例：

- `/blog/*` - blogディレクトリ内のすべてのページを対象にします
- `*/2023/*` - 2023を含むURLすべてを対象にします

入力例：
```
https://example.com/page1
https://example.com/page2
/contact
/about-us
*/exclude-this-part/*
```

**注意**: URLの末尾のスラッシュは自動的に削除されるため、`/contact/` と `/contact` は同じものとして扱われます。

## トラブルシューティング

URLが正しく除外されない場合は、以下の点を確認してください：

1. デバッグモードを有効にして、URL処理のログを確認
2. URLの形式が正しいか（絶対URLと相対URL）
3. ワイルドカードの使用方法が適切か

デバッグログは `wp-content/plugins/wp-llms-txt-generator/logs/url_debug.log` に保存されます。

## オリジナルからの主な変更点

- 投稿タイプごとの見出し表示機能の追加
- URL除外処理の大幅な改善（末尾のスラッシュの正規化など）
- 自動更新機能の追加
- 定期実行機能の追加
- カスタムヘッダーテキスト機能の追加
- デバッグモードの追加
- UI/UXの改善（タブインターフェース、ファイル情報の詳細表示など）

詳細な変更履歴は[CHANGELOG.md](CHANGELOG.md)を参照してください。

## ライセンス

GPL v2（元のプラグイン「LLMs-Full.txt and LLMs.txt Generator v1.9」をベースに開発）

## 開発者情報

- 改修: [いつもの匠](https://twitter.com/itsumonotakumi)
- ブログ: [ガジェットレビューの匠](https://mobile-cheap.jp)
- フォーク元プラグインの開発者: [rankth](https://profiles.wordpress.org/rankth/)

### 開発環境のセットアップ

1. Composerの依存関係をインストールします：
   ```
   composer install
   ```

2. コードスタイルチェックを実行します：
   ```
   composer phpcs
   ```

3. 自動的にコードスタイルを修正します：
   ```
   composer phpcbf
   ```

## 連絡先情報

- メールアドレス: [llms-txt@takulog.info](mailto:llms-txt@takulog.info)
- ホームページ: [https://mobile-cheap.jp](https://mobile-cheap.jp)
- X (Twitter): [@itsumonotakumi](https://x.com/itsumonotakumi)
- Threads: [@itsumonotakumi](https://www.threads.net/@itsumonotakumi)
- YouTube: [@itsumonotakumi](https://www.youtube.com/@itsumonotakumi)
