# agent-browser 利用ガイド（AIエージェント向け）

あなた（AIエージェント）がユーザーのPC上で agent-browser を使ってWebサイトの構造解析・操作を行うための手順書です。

---

## 環境情報

- OS: Windows 11
- agent-browser: v0.20.5（グローバルインストール済み）
- Chrome: 146.0.7680.80（`C:\Users\owner\.agent-browser\browsers\` にインストール済み）
- システムChrome: `C:\Program Files\Google\Chrome\Application\chrome.exe`
- デスクトップに「Chrome（デバッグ）」ショートカットあり（`--remote-debugging-port=9222`）

---

## 基本原則

### ユーザーがログイン → AIが引き継ぐ

このPCでは「人間がログインし、AIが操作を引き継ぐ」運用方針です。
AIがログイン処理を行う必要はありません。

```
人間: Chrome（デバッグ）で起動 → 対象サイトにログイン
AI:   agent-browser --auto-connect で接続 → 操作・解析
```

---

## 接続方法

### パターン1: ユーザーがChromeでログイン済みの場合（最も一般的）

```bash
# ログイン済みChromeに接続
npx agent-browser --auto-connect snapshot
```

ユーザーが「Chrome（デバッグ）」ショートカットからChromeを起動し、
対象サイトにログインした状態でAIに依頼してきます。
`--auto-connect` でそのChromeのセッション（cookie・認証状態）をそのまま使えます。

### パターン2: 認証状態が保存済みの場合

```bash
# 保存済みの認証で接続
npx agent-browser --state ./auth/<サービス名>-auth.json open <URL>
```

### パターン3: 公開ページ（ログイン不要）の場合

```bash
# agent-browser内蔵のChromeを使用
npx agent-browser open <URL>

# システムChromeを明示する場合
npx agent-browser --executable-path "C:\Program Files\Google\Chrome\Application\chrome.exe" open <URL>
```

※ 既にデーモンが動いている場合は先に `npx agent-browser close` してから `--executable-path` を指定。

---

## 主要コマンド

### 構造解析（最も頻繁に使う）

```bash
# アクセシビリティツリー全体（AI向け、ref番号付き）
npx agent-browser --auto-connect snapshot

# 操作可能な要素のみ（フォーム調査に最適）
npx agent-browser --auto-connect snapshot -i

# JavaScript実行でDOM情報を抽出
npx agent-browser --auto-connect eval "<JavaScriptコード>"
```

### スクリーンショット

```bash
# 表示中の画面
npx agent-browser --auto-connect screenshot ./screenshot.png

# ページ全体
npx agent-browser --auto-connect screenshot --full ./full-page.png

# アノテーション付き（要素番号をオーバーレイ）
npx agent-browser --auto-connect screenshot --annotate ./annotated.png
```

### UI操作

```bash
# クリック（ref番号はsnapshotで確認）
npx agent-browser --auto-connect click @e15

# テキスト入力
npx agent-browser --auto-connect fill @e20 "入力テキスト"

# キー押下
npx agent-browser --auto-connect press Enter

# スクロール
npx agent-browser --auto-connect scroll down 500
```

### 認証状態の管理

```bash
# 現在のセッションを保存
npx agent-browser --auto-connect state save ./auth/<名前>.json

# 保存した認証を読み込み
npx agent-browser state load ./auth/<名前>.json

# 保存済み一覧
npx agent-browser state list
```

### ブラウザ管理

```bash
# 閉じる
npx agent-browser close

# 現在のURL確認
npx agent-browser --auto-connect get url

# ページタイトル確認
npx agent-browser --auto-connect get title
```

---

## eval でよく使うパターン

### セクション・ID一覧の取得

```bash
npx agent-browser --auto-connect eval "[...document.querySelectorAll('[id]')].map(e => ({id: e.id, tag: e.tagName, class: e.className})).filter(e => e.tag !== 'SCRIPT' && e.tag !== 'STYLE' && e.tag !== 'LINK')"
```

### CSS Custom Properties（デザイントークン）の取得

```bash
npx agent-browser --auto-connect eval "JSON.stringify((() => { const s = getComputedStyle(document.documentElement); const v = {}; for (const p of s) { if (p.startsWith('--')) v[p] = s.getPropertyValue(p).trim(); } return v; })(), null, 2)"
```

### 外部ライブラリの取得

```bash
npx agent-browser --auto-connect eval "JSON.stringify({scripts: [...document.querySelectorAll('script[src]')].map(s => s.src), styles: [...document.querySelectorAll('link[rel=stylesheet]')].map(l => l.href)}, null, 2)"
```

### 特定要素のcomputedStyle取得

```bash
npx agent-browser --auto-connect eval "JSON.stringify((() => { const el = document.querySelector('h1'); const cs = getComputedStyle(el); return {text: el.textContent, fontSize: cs.fontSize, lineHeight: cs.lineHeight, fontWeight: cs.fontWeight, fontFamily: cs.fontFamily, color: cs.color}; })(), null, 2)"
```

### フォーム要素の全体取得

```bash
npx agent-browser --auto-connect eval "JSON.stringify([...document.querySelectorAll('input,textarea,select,button')].map(i => ({tag:i.tagName, type:i.type, id:i.id, name:i.name, placeholder:i.placeholder, required:i.required, class:i.className})), null, 2)"
```

### メディアクエリ（breakpoint）の取得

```bash
npx agent-browser --auto-connect eval "JSON.stringify((() => { const bps = new Set(); [...document.styleSheets].forEach(s => { try { [...s.cssRules].forEach(r => { if (r.media) bps.add(r.media.mediaText); }); } catch(e) {} }); return [...bps]; })(), null, 2)"
```

---

## 注意事項

### Windows固有の問題

- headlessモードではcookieが保持されない場合がある
  → `AGENT_BROWSER_HEADED=1` を設定するか `--headed` フラグを使用
- パスの区切りは `/` でも `\` でも動作する

### --auto-connect が失敗する場合

1. ユーザーに「Chrome（デバッグ）で起動していますか？」と確認
2. 普通のChromeが裏で動いていないか確認を依頼
3. それでもダメなら `npx agent-browser close` 後に再試行

### eval の文字列エスケープ

- eval に渡すJSコードにシングルクォートとダブルクォートが混在する場合、
  bash上での引用符のネストに注意
- 複雑なコードは一時ファイルに書き出して実行する方が安全：
  ```bash
  npx agent-browser --auto-connect eval "$(cat ./query.js)"
  ```

### セキュリティ

- `state save` で保存した .json にはセッショントークンが含まれる
- .gitignore に `auth/*.json` を追加すること
- 認証ファイルの共有・コミットは禁止

---

## 対象サービス一覧（このPCで使用）

| サービス | 認証方式 | 推奨接続方法 |
|---------|---------|-------------|
| 楽楽精算 | 企業コード + ID/PW | --auto-connect → state save |
| レコル | SSO | --auto-connect（毎回） |
| SBポータル | 2FA | --auto-connect（毎回） |
| ドライブレポート | ID/PW | --auto-connect → state save |
| 公開Webサイト | なし | open <URL> |

---

## 参考

- 公式ドキュメント: https://agent-browser.dev/
- Skills一覧: https://agent-browser.dev/skills
- セッション管理: https://agent-browser.dev/sessions
- GitHub: https://github.com/vercel-labs/agent-browser
