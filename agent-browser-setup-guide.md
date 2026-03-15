# agent-browser セットアップガイド

AIエージェントが「ログイン済みのChrome」に接続して、Webサイトの構造解析や操作を行うための環境構築手順です。

---

## 前提条件

- Windows 10 / 11
- Google Chrome がインストール済み
- Node.js 18以上がインストール済み（https://nodejs.org/）

---

## Step 1: agent-browser のインストール

コマンドプロンプト または PowerShell を開いて以下を実行：

```
npm install -g agent-browser
```

インストール確認：

```
npx agent-browser --version
```

バージョン番号が表示されればOK。

---

## Step 2: Chrome のインストール（agent-browser用）

```
npx agent-browser install
```

これにより agent-browser 専用の Chrome が `C:\Users\<ユーザー名>\.agent-browser\browsers\` にダウンロードされます。

---

## Step 3: デバッグ用 Chrome ショートカットの作成

### 方法A: コマンドで自動作成（推奨）

PowerShell を開いて以下をコピペ実行：

```powershell
$WshShell = New-Object -comObject WScript.Shell
$Desktop = [Environment]::GetFolderPath('Desktop')
$Shortcut = $WshShell.CreateShortcut("$Desktop\Chrome（デバッグ）.lnk")
$Shortcut.TargetPath = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$Shortcut.Arguments = '--remote-debugging-port=9222'
$Shortcut.Description = 'agent-browser接続用 Chrome（ポート9222）'
$Shortcut.IconLocation = 'C:\Program Files\Google\Chrome\Application\chrome.exe,0'
$Shortcut.Save()
Write-Host "ショートカットを作成しました: $Desktop\Chrome（デバッグ）.lnk"
```

### 方法B: 手動作成

1. デスクトップを右クリック →「新規作成」→「ショートカット」
2. 場所に以下を入力：
   ```
   "C:\Program Files\Google\Chrome\Application\chrome.exe" --remote-debugging-port=9222
   ```
3. 名前を「Chrome（デバッグ）」にする
4. 「完了」をクリック

---

## 使い方

### 基本の流れ

```
① 普通のChromeを全部閉じる（タスクバーに残っているものも含む）
      ↓
② デスクトップの「Chrome（デバッグ）」をダブルクリック
      ↓
③ 普通のChromeと同じように使う（見た目は同じ）
      ↓
④ 対象のWebサイト（楽楽精算等）にログイン
      ↓
⑤ AIエージェントに作業を依頼
      ↓
⑥ AIが --auto-connect でログイン済みChromeに接続して操作
```

### 重要：普通のChromeを先に閉じる理由

普通のChromeとデバッグ用Chromeは同時に起動できません。
デバッグ用Chromeを起動する前に、タスクバーやシステムトレイに残っている
Chromeのプロセスを全て終了してください。

確認方法：タスクマネージャー（Ctrl+Shift+Esc）→「Google Chrome」が残っていないことを確認。

---

## AIエージェント側のコマンド例

### サイトの構造を取得

```bash
# ログイン済みChromeに接続してアクセシビリティツリーを取得
npx agent-browser --auto-connect snapshot

# 操作可能な要素だけ取得
npx agent-browser --auto-connect snapshot -i
```

### スクリーンショット撮影

```bash
npx agent-browser --auto-connect screenshot ./screenshot.png

# ページ全体
npx agent-browser --auto-connect screenshot --full ./full-page.png
```

### JavaScript実行でデータ抽出

```bash
npx agent-browser --auto-connect eval "document.title"
```

### 認証状態の保存と再利用

```bash
# ログイン済みセッションを保存
npx agent-browser --auto-connect state save ./auth/rakuraku-auth.json

# 保存したセッションで接続（次回以降、人間のログイン不要）
npx agent-browser --state ./auth/rakuraku-auth.json open https://app.rakurakuseisan.jp/
```

※ セッションの有効期限が切れたら、再度①〜④を行って state save し直してください。

---

## トラブルシューティング

### 「Auto-launch failed」と表示される

→ `npx agent-browser install` を実行してChromeをインストール。

### 「--auto-connect」で接続できない

→ 以下を確認：
  - デバッグ用Chrome（ポート9222）で起動しているか
  - 普通のChromeが裏で動いていないか（タスクマネージャーで確認）

### headlessモードでcookieが消える（Windows）

→ 環境変数を設定：

```
set AGENT_BROWSER_HEADED=1
```

または PowerShell：

```powershell
$env:AGENT_BROWSER_HEADED = "1"
```

### Node.js がインストールされていない

→ https://nodejs.org/ から LTS版 をダウンロードしてインストール。
   インストール後、コマンドプロンプトを開き直して `node --version` で確認。

---

## セキュリティに関する注意

- `--remote-debugging-port=9222` で起動したChromeは、
  同じPC上のプログラムからブラウザを操作できる状態になります。
- 外部ネットワークからの接続はデフォルトでブロックされます（localhost限定）。
- `state save` で保存した認証ファイル（.json）にはセッション情報が含まれます。
  - 他人と共有しないでください
  - Gitリポジトリにコミットしないでください（.gitignoreに追加推奨）
  - 不要になったら削除してください

---

## 環境変数の一覧（オプション）

| 変数名 | 説明 | 例 |
|--------|------|-----|
| `AGENT_BROWSER_HEADED` | ブラウザ画面を表示（cookie保持） | `1` |
| `AGENT_BROWSER_AUTO_CONNECT` | 常にauto-connect | `1` |
| `AGENT_BROWSER_SESSION_NAME` | セッション名（自動保存） | `rakuraku` |
| `AGENT_BROWSER_ENCRYPTION_KEY` | 認証ファイルの暗号化キー | 64文字のhex |

---

## 作成日

2026-03-15
