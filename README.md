# Lian-Heart_web

リアンハートの LP を `Next.js` で運用するリポジトリです。  
公開先は `Vercel`、フォーム受信は `Google Apps Script` を前提にしています。

## ローカル起動

```bash
cd "C:\ProgramData\Generative AI\Github\Lian-Heart_web"
npm.cmd install
npm.cmd run dev
```

ブラウザで `http://localhost:3000` を開きます。

## 必須ファイル

- `src/app/page.tsx`: LP 本体
- `src/app/layout.tsx`: SEO / OGP / メタデータ
- `src/app/api/contact/route.ts`: フォーム送信の受け口
- `apps-script/contact-webapp.gs`: Apps Script 受信スクリプト

## 環境変数

`.env.local` を作成して設定します。

```env
GAS_WEB_APP_URL=https://script.google.com/macros/s/XXXXX/exec
NEXT_PUBLIC_SITE_URL=https://your-project.vercel.app
```

- `GAS_WEB_APP_URL`: Apps Script Web App の `/exec`
- `NEXT_PUBLIC_SITE_URL`: 公開 URL

独自ドメイン切替後は `NEXT_PUBLIC_SITE_URL` を独自ドメインへ更新します。

## Apps Script 設定

`apps-script/contact-webapp.gs` を新規 Apps Script プロジェクトへ貼り付けてください。

Script Properties:

- `SHEET_ID`
- `NOTIFY_TO`

スプレッドシート保存列:

- `timestamp`
- `name`
- `furigana`
- `phone`
- `email`
- `relationship`
- `area`
- `facilityType`
- `careLevel`
- `budget`
- `moveInDate`
- `message`
- `privacy`
- `sourceUrl`
- `userAgent`

Apps Script の詳細は `apps-script/README.md` を参照してください。

## GitHub へ配置

このリポジトリはまだ remote 未設定です。GitHub に新規 repo を作成した後、以下を実行します。

```bash
git remote add origin <your-repository-url>
git push -u origin main
```

## Vercel 公開

### 1. 事前確認

```bash
npm.cmd run build
```

### 2. Vercel へログイン

```bash
npm.cmd run vercel:whoami
npm.cmd run vercel:link
```

未ログインの場合は、先に以下を実行します。

```bash
npx vercel login
```

### 3. 環境変数を Vercel に設定

Vercel Dashboard で以下を設定します。

- `GAS_WEB_APP_URL`
- `NEXT_PUBLIC_SITE_URL`

### 4. Preview / Production デプロイ

```bash
npm.cmd run deploy:preview
npm.cmd run deploy:prod
```

### 5. 独自ドメイン接続

1. Vercel Dashboard の `Domains` で独自ドメインを追加
2. DNS を Vercel 指定値へ向ける
3. `NEXT_PUBLIC_SITE_URL` を独自ドメインへ更新
4. 再デプロイ

## 公開後チェック

- LP が表示される
- アンカーリンクが動く
- 電話 / LINE / フォーム導線が動く
- `/api/contact` 経由で Apps Script へ保存される
- 通知メールが届く
- 独自ドメイン接続後に SSL が有効
