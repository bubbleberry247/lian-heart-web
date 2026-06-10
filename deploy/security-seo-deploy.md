# セキュリティ強化・SEO/AI検索対策 デプロイ手順

対象: 本番 http://xs627352.xsrv.jp/ （Xserver / WordPress）
ベーステーマ: `lian-heart-custom-theme-clean-family`（本番 family-next の後継）
作業ブランチ: `feat/security-seo-hardening`

この変更で入るもの:
- フォーム処理にレート制限(3req/60s)とPII配慮ログ（submission_idのみ）を追加（rest-contact.php）
- ユーザー列挙対策（REST users制限 / author archive無効化 / ?author=リダイレクト）
- WordPressバージョン露出抑止（generator除去）、XML-RPC無効化
- セキュリティHTTPヘッダー（X-Frame-Options 他）、ログインエラー秘匿
- 代表者・所在地のサンプル値を実値へ統一（電話/FAX/メールは未確定のため据え置き）
- noindex は維持（`LH_FORCE_NOINDEX` 制御。公開は別途切替）

---

## 0. ロールバック確保（着手前に必ず実施）

「今の本番状態にいつでも戻せる」状態を3層で確保する。

1. **git 起点コミット（実施済み）**
   - 変更前の clean-family は起点コミット `4ef9fdd` に保存済み。
   - コード復元: `git checkout 4ef9fdd -- wordpress-theme/lian-heart-custom-theme-clean-family/` または `git revert`。

2. **本番テーマ＋wp-config の SFTP退避（デプロイ担当が実施）**
   - `deploy/sftp-config.example.ps1` をコピーして `deploy/sftp-config.ps1` を作成し、接続情報を記入（このファイルはコミットしない）。
   - 現行本番テーマ `wp-content/themes/lian-heart-custom-theme-clean-family-next` を丸ごとローカルへダウンロードし `deploy/artifacts/backup-prod-theme-YYYYMMDD-HHmm/` に退避。
   - `wp-config.php` も1ファイルだけバックアップ（`LH_FORCE_NOINDEX` 追記前の状態）。

3. **旧テーマフォルダ温存（デプロイ時の鉄則）**
   - 新テーマは**別フォルダ名** `lian-heart-custom-theme-clean-family-secure` でアップする。
   - 旧 `...-family-next` フォルダは表示確認が完了するまで**削除しない**。
   - ロールバック = WP管理画面で旧テーマを再有効化（即時復旧）。

---

## 1. ビルド

```powershell
# テーマを zip 化（deploy/artifacts/ に生成）
powershell -File deploy\build-theme-artifact.ps1 -ThemeDir wordpress-theme\lian-heart-custom-theme-clean-family
```

> ビルド前にローカルで構文チェック（任意・推奨）:
> `php -l wordpress-theme/lian-heart-custom-theme-clean-family/inc/theme-setup.php`
> `php -l wordpress-theme/lian-heart-custom-theme-clean-family/inc/rest-contact.php`
> （本リポジトリでは両ファイル `No syntax errors detected` を確認済み）

## 2. アップロード（新フォルダ名・旧温存）

```powershell
# sftp-config.ps1 の DeployFolderName を lian-heart-custom-theme-clean-family-secure に設定してから実行
powershell -File deploy\push-theme-via-sftp.ps1
```

リモートに以下が揃うことを確認:
`style.css` / `functions.php` / `front-page.php` / `inc/theme-setup.php` / `inc/rest-contact.php` / `page-templates/template-referrer.php` / `assets/`

> ⚠️ **アップロード後は必ずパーミッションを修正する**（Xserver SFTPデプロイの鉄則。忘れると403）。
> 目安: ディレクトリ 755 / ファイル 644。`push-theme-via-sftp.ps1` に chmod 処理が無ければ手動で設定する。

## 3. wp-config.php に noindex 強制を追記（サーバー側）

`wp-config.php` の `/* That's all, stop editing! */` の**直前**に追記:

```php
define('LH_FORCE_NOINDEX', true);
```

（サンプルは `wordpress-theme/wp-config.lian-heart.example.php` 参照。公開時に false/削除で解除）

## 4. テーマ有効化

WP管理画面 `外観 > テーマ` で新テーマを有効化。
有効化時に紹介元ページ `/medical-care-professionals/` が自動作成される（`after_switch_theme` フック）。

---

## 5. デプロイ後検証（ライブ http で確認）

```bash
# generator が消えている（出力なしが正解）
curl -s http://xs627352.xsrv.jp/ | grep -i generator

# REST users が塞がれている → 401 or 404（現状は200で漏洩）
curl -s -o /dev/null -w "%{http_code}\n" http://xs627352.xsrv.jp/wp-json/wp/v2/users

# ?author=1 がトップへ301
curl -s -o /dev/null -w "%{http_code}\n" "http://xs627352.xsrv.jp/?author=1"

# /author/... がトップへ301
curl -s -o /dev/null -w "%{http_code}\n" "http://xs627352.xsrv.jp/author/admin/"

# セキュリティヘッダーが付与されている
curl -sI http://xs627352.xsrv.jp/ | grep -iE "x-frame-options|x-content-type-options|referrer-policy"

# noindex は維持されている（公開前なので noindex,nofollow が正解）
curl -s http://xs627352.xsrv.jp/ | grep -i '<meta name="robots"'

# 紹介元ページが生きている
curl -s -o /dev/null -w "%{http_code}\n" http://xs627352.xsrv.jp/medical-care-professionals/

# partner-support セクションが本番に出ている（レイアウト維持の確認）
curl -s http://xs627352.xsrv.jp/ | grep -c partner-support
```

期待値: generator消滅 / users=401or404 / author系=301 / セキュリティヘッダあり / robots=noindex維持 / 紹介元200 / partner-support>0

問題があれば即ロールバック（旧テーマ再有効化）。

---

## 6. フォーム送信のスモークテスト

新テーマ有効化後、サイトの入居相談フォームから1件テスト送信し:
- 受信メールが届く（recipient_email / admin_email）
- 連続送信で4回目に 429（レート制限）になる
- サーバーの error_log に `[LH Contact] OK id=...` が submission_id のみで記録され、氏名/メール/IPが出ていない

を確認する。
