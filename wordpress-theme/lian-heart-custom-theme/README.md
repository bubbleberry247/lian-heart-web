# Lian Heart Custom Theme

このディレクトリは、参考LPの構造・余白・モーション設計を WordPress の専用カスタムテーマとして再構築したものです。

## 配置先

`wp-content/themes/lian-heart-custom-theme`

## 前提

- WordPress 6.4+
- PHP 8.1+
- ACF Pro
- Google Apps Script の Web App URL

## 導入手順

1. このフォルダを `wp-content/themes/` に配置
2. WordPress 管理画面で `Lian Heart Custom Theme` を有効化
3. `ACF Pro` を有効化
4. 管理画面に追加される `Lian Heart Theme` オプションで各セクションを設定
5. `LH_GAS_WEB_APP_URL` を `wp-config.php` に定義、またはオプションの GAS URL を設定
6. フロントページを表示確認

## フォーム送信先

優先順:

1. `LH_GAS_WEB_APP_URL` 定数
2. ACF オプション `お問い合わせ > GAS Web App URL`

## 実装内容

- 全画面ヒーロー + `Swiper fade`
- `GSAP ScrollTrigger` ベースの到達アニメーション
- 交互レイアウトのサービス / フローセクション
- sticky FAQ 見出し
- mobile ハンバーガー
- mobile 固定 CTA
- WordPress REST API 経由のフォーム送信
- GAS へのサーバーサイド転送

## 注意

- 画像は仮のプレースホルダーが入ります。運用時に必ず差し替えてください。
- トップページ本文も ACF オプションで差し替えてください。
- Instagram / Google Map は初期実装に含めていません。
