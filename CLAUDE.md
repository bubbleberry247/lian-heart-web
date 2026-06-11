# Lian-Heart_web

Next.js で運用する LP リポジトリです。公開は Vercel、フォーム受信は Google Apps Script を前提にしています。

## Key Areas
- `src/app/`: LP 本体、レイアウト、API ルート
- `apps-script/`: フォーム受信用 Apps Script
- `docs/`, `skills/`: 補助資料と運用支援

## Working Notes
- LP の見た目を変えるときは、既存のブランドトーンと導線を維持することを優先します。
- フォームまわりを変えるときは Next.js 側と Apps Script 側の契約を一緒に確認します。
- ローカル起動や必須ファイルの案内が変わる場合は `README.md` を更新します。
