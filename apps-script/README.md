# Google Apps Script 設定（フォーム受信）

この LP の ` /api/contact` は、Google Apps Script の Web App へ内容をPOSTします。
以下を行うとフォーム送信の保存と通知が動作します。

## 1) Google Apps Script の準備

1. Google Apps Script で新規プロジェクトを作成
2. `apps-script/contact-webapp.gs` を貼り付け
3. スクリプトプロパティを設定
   - `SHEET_ID`: 保存先スプレッドシートID
   - `NOTIFY_TO`: 通知先メールアドレス（例: `info@example.co.jp`）
4. スプレッドシートを用意
   - 1行目はヘッダーとして自動生成されます
   - 保存先列: `timestamp, name, furigana, phone, email, relationship, area, facilityType, careLevel, budget, moveInDate, message, privacy, sourceUrl, userAgent`

## 2) デプロイ

- ウェブアプリとしてデプロイ
- 公開URLの `/exec` を `GAS_WEB_APP_URL` に設定

## 3) Next.js 側環境変数

`.env`（または Vercel の環境変数）に設定:

- `GAS_WEB_APP_URL`
- `NEXT_PUBLIC_SITE_URL`

> 開発中は `NEXT_PUBLIC_SITE_URL` を省略しても問題ありません。
