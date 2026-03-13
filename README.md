# Lian-Heart_web

新しいウェブサイト用の独立した Next.js リポジトリです。`archi-16w-training` とは分離して運用できます。

## Setup

```bash
cd "C:\ProgramData\Generative AI\Github\Lian-Heart_web"
npm.cmd install
npm.cmd run dev
```

ブラウザで `http://localhost:3000` を開くと初期トップページを確認できます。

## First edits

- `src/app/page.tsx`: ファーストビューと導線を本番向けに差し替える
- `src/app/layout.tsx`: サイト名とメタデータを更新する
- `src/app/globals.css`: ブランドカラーとフォントを調整する

## GitHub remote

```bash
git remote add origin <your-repository-url>
git push -u origin main
```
