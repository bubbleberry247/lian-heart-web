# WordPress導入手順

## 1. テーマをアップロード

- 管理画面 `外観 > テーマ > 新規追加 > テーマのアップロード`
- [lian-heart-custom-theme.zip](C:/ProgramData/Generative AI/Github/Lian-Heart_web/wordpress-theme/lian-heart-custom-theme.zip) を指定
- インストール後に有効化

ZIP を使わない場合:

- [lian-heart-custom-theme](C:/ProgramData/Generative AI/Github/Lian-Heart_web/wordpress-theme/lian-heart-custom-theme) を `wp-content/themes/` に配置

## 2. 必須プラグイン

- `Advanced Custom Fields Pro`

有効化後、管理画面左に `Lian Heart Theme` が出ます。

## 3. フロントページ設定

- `設定 > 表示設定`
- `ホームページの表示` を `固定ページ` に変更
- 任意の固定ページをホームページに指定

`front-page.php` があるため、トップページ表示時にテーマ側のLPが使われます。

## 4. GAS 送信先

優先順:

1. `wp-config.php` の `LH_GAS_WEB_APP_URL`
2. `Lian Heart Theme > お問い合わせ > GAS Web App URL`

本番は `wp-config.php` 側の定数管理を推奨します。

## 5. 管理画面で差し替える項目

- ブランド名
- ヒーロー文言
- ヒーロー画像 desktop 3枚 / mobile 3枚
- サービスの特徴
- 入居相談の流れ
- 代表挨拶
- FAQ
- 施設の種類
- 会社情報
- 問い合わせ文言

## 6. 実運用前に最低限差し替えるもの

- 会社名
- 住所
- 電話番号
- メールアドレス
- LINE URL
- ヒーロー画像
- 会社情報画像
- GAS Web App URL

## 7. ステージング

テーマ側で `wp_get_environment_type() !== 'production'` の場合は `noindex` を出します。

本番化する前に、サーバー側の環境タイプを `production` にしてください。
