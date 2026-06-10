# Xserver サーバー設定 ＆ 公開切替 ランブック

対象: 本番 xs627352.xsrv.jp（Xserver / WordPress）
担当: サーバー運用者（Xserverサーバーパネルでの作業が中心）

テーマ側のセキュリティ強化・SEO構造は `deploy/security-seo-deploy.md` でデプロイ済みである前提。
ここではサーバー設定（HTTPS等）と、実データ確定後の公開切替を扱う。

---

## A. HTTPS を有効化（最優先・現状は証明書エラーで機能していない）

現状 `https://xs627352.xsrv.jp/` は証明書のドメイン不一致（`ERR_TLS_CERT_ALTNAME_INVALID`）で開けない。SEO・セキュリティ両面で致命的なので最優先で是正する。

1. Xserverサーバーパネル → **SSL設定** → 対象ドメインを選択。
2. **「独自SSL設定追加（無料 / Let's Encrypt）」** を実行。対象ドメインに一致する証明書が発行される。
3. 反映に最大1時間。`curl -sI https://xs627352.xsrv.jp/` が証明書エラーなく `200` を返すことを確認。

## B. http → https 常時化

1. サーバーパネル → **.htaccess編集**（対象ドメインの `public_html`）の先頭に追記:

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</IfModule>
```

2. WP管理画面 → **設定 → 一般** で「WordPress アドレス」「サイトアドレス」を `https://` に変更（混在コンテンツ防止）。
3. `curl -s -o /dev/null -w "%{http_code}\n" http://xs627352.xsrv.jp/` が `301`（→https）になることを確認。

## C. .htaccess によるサーバーレベル堅牢化

`public_html/.htaccess` に追記（テーマ側の対策と二重化）:

```apache
# XML-RPC を遮断
<Files xmlrpc.php>
  Require all denied
</Files>

# 機微ファイルへの直アクセスを拒否
<FilesMatch "^(wp-config\.php|\.htaccess|readme\.html|license\.txt)$">
  Require all denied
</FilesMatch>

# ディレクトリ一覧表示を無効化
Options -Indexes
```

### セキュリティHTTPヘッダー（CSPはReport-Onlyから段階導入）

X-Frame-Options 等はテーマ側で付与済み。CSP はサーバー側で **Report-Only** から開始し、ブラウザのコンソール/レポートで違反を確認しながら調整する（いきなり enforce しない）。

```apache
<IfModule mod_headers.c>
  # まずは違反を観測するだけ（サイトは壊れない）。問題なければ -Report-Only を外して enforce。
  Header set Content-Security-Policy-Report-Only "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-src https://www.google.com https://maps.google.com; connect-src 'self' https://script.google.com"
</IfModule>
```

> 外部依存: Google Fonts(fonts.googleapis.com/gstatic.com) / jsDelivr Swiper(cdn.jsdelivr.net) / cdnjs GSAP(cdnjs.cloudflare.com) / Google Maps iframe(www.google.com,maps.google.com) / GAS(script.google.com)。
> **HSTS** は HTTPS が安定稼働してから、短い `max-age` で開始する（SSL確立前に付けない）。

## D. Xserver パネルの保護機能

- **WordPressセキュリティ設定**: ログイン試行回数制限・国外IPアクセス制限・大量コメント/トラックバック制限を ON。
- **WAF設定**: ON（推奨）。
- 有効化後、正規のログインや管理操作がブロックされないことを確認。

---

## E. 公開切替チェックリスト（noindex解除＝検索掲載開始）

**公開判断は、電話・メール・住所などの実データが確定してから。** 確定前に解除すると未確定情報が検索・AIに載る。

1. [ ] `inc/theme-setup.php` の `lh_theme_defaults()` 内 `company.rows`（電話 `052-000-0000` / FAX `052-000-0001` / メール `info@example.co.jp`）と `hero.ctas` の `tel:052-000-0000` を実値に更新。代表者・所在地は統一済み（西田 江里 / 名駅4-24-5 第2森ビル401）。
2. [ ] 実値が `lh_is_placeholder_value()` の `/example|0000|○○|sample/i` に当たらないことを確認（当たると JSON-LD から除外され続ける）。
3. [ ] **公開操作**: `wp-config.php` の `LH_FORCE_NOINDEX` を `false` に変更、またはこの行を削除。
   - これだけで robots メタが `index,follow,...` に切替、JSON-LD（LocalBusiness等）と robots.txt のAIクローラ方針が自動で有効化される。
4. [ ] パーマリンクを **設定 → パーマリンク → 「投稿名」** に設定 → `https://xs627352.xsrv.jp/wp-sitemap.xml` が生成されることを確認（現状404の解消）。
5. [ ] 解除後のライブ確認:
   - `<meta name="robots">` が `index,follow` に変わっている
   - `application/ld+json` に LocalBusiness/Organization/WebSite/Service/WebPage(/FAQPage) が出ている
   - `robots.txt` に AI検索bot許可（OAI-SearchBot/Claude-SearchBot/PerplexityBot 等）と学習bot拒否（GPTBot/ClaudeBot/CCBot/Google-Extended）が出ている
6. [ ] Google リッチリザルトテストで公開URLを検証（telephone/email/address が実値で出る）。

## F. 検索・AI検索への登録（公開後）

- [ ] **Google Search Console**: ドメインプロパティで登録（DNS TXT認証）。`wp-sitemap.xml` を送信。
- [ ] **Bing Webmaster Tools**: 登録（GSCからインポート可）。sitemap送信。Copilot等のAI検索はBing経由が効く。
- [ ] **Google ビジネスプロフィール / Bing Places**: ローカルビジネス情報を整備（LocalBusiness schemaと住所・電話を一致させる）。
- [ ] AI検索（ChatGPT/Claude/Perplexity/Google AI Overviews）で指名検索・サービス検索を定期確認。

> 参考: `docs/seo-ai-optimization-checklist.md`（従来SEO＋AI検索対応チェックリスト）
