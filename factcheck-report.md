# ファクトチェックレポート: koureishashoukai.com 仕様書の検証結果

agent-browser v0.20.5 + Chrome 146 で参考サイトを実機解析し、仕様書との差異を検証した結果です。
仕様書の修正・補完に使ってください。

---

## A. 仕様書の誤り（要修正）

### A-1. カラーパレット

| CSS変数 | 仕様書の値 | 実測値 | 判定 |
|---------|-----------|--------|------|
| `--foreground-inverted` | `#ffffff` | **`#fbfbfb`** | 要修正 |
| `--gradient-color-2` | `#ffeaed` | **`#f495a2`** | 要修正（primaryと同色） |
| `--primary-strong: #e0354c` | 定義あり | **実サイトにこの変数は存在しない** | 削除 |

実測されたCSS Custom Properties（WordPress theme.json由来）:
```
--wp--preset--color--foreground: #0c1c1f           ✅ 仕様書と一致
--wp--preset--color--foreground-inverted: #fbfbfb   ❌ 仕様書は#ffffff
--wp--preset--color--primary: #f495a2               ✅ 一致
--wp--preset--color--background: #ffffff             ✅ 一致
--wp--preset--color--background-secondary: #fde8eb   ✅ 一致
--wp--preset--color--gradient-color-1: #fde8eb       ✅ 一致
--wp--preset--color--gradient-color-2: #f495a2       ❌ 仕様書は#ffeaed
--wp--preset--color--warning: #ff3364                ⚠ 仕様書に記載なし
--wp--preset--color--custom-primary-opacity-mode: #f495a2
--wp--preset--color--custom-background-opacity-mode: #ffffff70
```

### A-2. bodyの文字サイズ

| 項目 | 仕様書 | 実測値（computedStyle） | 説明 |
|------|--------|------------------------|------|
| body font-size | 12px | **10.5162px** | CSSは12pxだがvw換算で縮小 |
| body line-height | 24px | **21.0325px** | 同上 |

仕様書にはCSSの指定値を書いているが、実際のレンダリングはビューポート幅に依存するvw単位で縮小される。
→ 仕様書に「※vw併用のため実際の表示サイズはビューポート依存」の注記を追加推奨。

### A-3. レスポンシブのbreakpoint

仕様書は3段階のみ記載だが、**実際は7段階以上**:
```
(max-width: 767px)                            ← mobile    ✅ 仕様書記載
(min-width: 768px)                            ← tablet~   ✅ 仕様書記載
(min-width: 768px) and (max-width: 1023px)    ← tablet    ✅ 仕様書記載
(min-width: 1024px)                           ← desktop   ✅ 仕様書記載
(min-width: 1486px)                           ← wide      ❌ 仕様書に記載なし
(min-width: 1600px)                           ← wider     ❌ 仕様書に記載なし
(min-width: 1920px)                           ← fullHD    ❌ 仕様書に記載なし
(min-width: 768px) and (max-width: 1260px)    ← 中間      ❌ 仕様書に記載なし
(hover: hover)                                ← hover     ❌ 仕様書に記載なし
(prefers-reduced-motion: reduce)              ← a11y      ❌ 仕様書に記載なし
```

→ 特に `1486px`, `1600px`, `1920px` の wide breakpoint と `prefers-reduced-motion` 対応は再現時に無視すると大画面での見た目が崩れる。

---

## B. 仕様書の見落とし（要追加）

### B-1. Vue.js 2.6.14 が読み込まれている

```
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js"></script>
```
→ Instagramギャラリー (`#js-instagram-gallery`) で使用。
仕様書の「4. 必須ライブラリ」にVue.jsの記載なし。
Instagramセクションを実装しない場合は不要だが、事実として記録すべき。

### B-2. テーマ名「yadori」+ 共有テーマ基盤

```
テーマ: wp-content/themes/yadori/
CDN:    cdn.otemaru.com/wp-oem-css/  (共有CSS)
        cdn.otemaru.com/wp-oem-js/   (共有JS)
```
→ 完全な独自テーマではなく、`otemaru.com` のOEMテーマ基盤（yadori）をベースにカスタマイズしている。
仕様書の「専用カスタムテーマ」という前提は間違いではないが、参考サイト自体がOEMテーマベースである点は認識すべき。

### B-3. Instagramセクションが実在する

```html
<aside id="instagram" class="instagram">
  <div id="js-instagram-gallery" class="instagram-gallery"> <!-- 24件の投稿 -->
```
→ 仕様書は「Instagram セクションは初期実装では作らない」としているが、参考サイトには24件のInstagram投稿ギャラリーが存在。Vue.js + 専用JSで実装。

### B-4. #shop-info にIframe（Google Map）が存在

```
#shop-info > generic > generic > generic > generic > Iframe
```
→ 仕様書は「Google Map は標準では表示しない」としているが、**実際にはiframeでGoogle Mapが埋め込まれている**。

### B-5. フォームの3段階UI

実測されたフォームボタン:
```
1. button[submit] name="verify"  class="...wp-element-button"       ← 確認ボタン
2. button[button] class="...form-back-btn"                          ← 戻るボタン
3. button[submit] name="send"    class="...wp-element-button"       ← 送信ボタン
```
→ フォームは「入力 → 確認（verify） → 戻る/送信（send）」の3段階UI。
仕様書は「送信後は同一ページで完了表示に切り替える」のみで、確認画面の存在に言及していない。

### B-6. プライバシーポリシー全文がフォーム内に埋め込み

フォーム内にreadonly textareaとしてプライバシーポリシー全文（8条構成）が直接埋め込まれている。
→ 別ページへのリンクではなく、フォーム内で閲覧+チェックボックスで同意する方式。

### B-7. トラッキング・マーケティング

```
Google Analytics:  GA-JLQFPCKQHZ
Google Ads:        AW-17745879701
LINE Tag Pixel:    411a8bf5-8c98-4fb5-bfd2-593c62817f5a
```
→ 仕様書にトラッキング要件の記載なし。再現時にこれらの計測タグの設置要否を確認すべき。

### B-8. 会社情報テーブルの実データ

```
運営会社:  株式会社SECRET MEDICINE
代表者:    吉村 貴成
所在地:    〒581-0004 大阪府八尾市東本町3丁目9-36 板倉第一ビル404号室
TEL:       072-993-3033
FAX:       072-993-3032
HP:        https://secretmedicine.site
```

### B-9. 関連施設の実データ（3件）

```
1. メディケア縁八尾
   → 公式HP未記載（snapshot上）

2. メディケア縁近鉄八尾駅前
   〒581-0803 大阪府八尾市光町1-38
   近鉄大阪線 近鉄八尾駅 徒歩3分
   HP: https://www.medicareen-yaoekimae.com/

3. メディケア縁ALOHA
   〒581-0845 大阪府八尾市上之島町北1丁目21-21
   近鉄大阪線 河内山本駅 徒歩18分
   HP: https://www.medicareen-aloha.com/
```

---

## C. 仕様書の補強データ（精度向上用）

### C-1. 全heading タイポグラフィ実測値

```
H1 hero:           48px / lh:96px / weight:700 / 游ゴシック
H2 concept:        36.598px / lh:73.196px / weight:700 / 游ゴシック
H2 section-title:  16px / lh:40px / weight:700 / 游ゴシック
H3 pride-point:    21.025px / lh:42.05px / weight:700 / 游ゴシック
H3 menu-item:      11.831px / lh:23.662px / weight:700 / 游ゴシック
H3 qa:             11.831px / lh:23.662px / weight:700 / 游ゴシック
H4 pride-catch:    36.808px / lh:57.789px / weight:700 / 游ゴシック
```

### C-2. CTA ボタン実測値

```
ヘッダーCTA:
  text: "お問い合わせ"
  bg: rgb(244, 149, 162) = #f495a2
  color: rgb(12, 28, 31) = #0c1c1f
  border-radius: 50px
  width: 220px
  height: 64px
```

### C-3. Swiper 実測構造

```
class: hero__slider swiper swiper-fade swiper-horizontal swiper-watch-progress swiper-backface-hidden
slides: 5枚（3枚 + loop用の前後2枚の複製）
画像:
  hero_slide_1_bg-1-scaled.jpg
  hero_slide_2_bg-scaled.jpg
  hero_slide_3_bg-scaled.jpg
```

### C-4. GSAPアニメーション用クラス一覧

```
is-animating              → ヒーロー文字に付与（フェードイン発火済み）
js-concept-visual-fx      → コンセプト画像3枚（ワイプ演出）
js-headline-fx            → セクション見出し全7箇所
js-pride-point-fx         → サービス特徴ブロック3箇所
js-menu-item-fx           → メニュー項目6箇所（menu + facility）
js-acc                    → FAQアコーディオン6件
js-menu                   → facility セクション全体
is-loading                → フォーム送信中アニメーション
scrolldown-indicator      → ヒーロー下部のスクロール誘導
```

### C-5. 固定CTA実測

```
desktop:
  - link "無料で相談する" [ref=e11]     ← 電話/問い合わせ
  - link "LINEで簡単相談" [ref=e12]     ← LINE

固定ページトップ:
  - div.fixed-pagetop-btn > a.fixed-pagetop-btn__link

フローティングCTA:
  - div.floting-btns__free-link > "LINEで簡単相談"
```

### C-6. WordPress 環境情報

```
WordPress: 6.5.5
テーマ: yadori（cdn.otemaru.com OEMベース）
テーマCSS:
  - reset.css (CDN)
  - swiper-bundle.min.css (CDN)
  - form.css (テーマ内)
  - style.css (テーマメイン)
  - front-style.css (フロントページ専用)
テーマJS:
  - libs.js (CDN共通)
  - MotionPathPlugin.min.js (GSAP)
  - swiper-bundle.min.js (Swiper 8)
  - core.js (テーマコア: GSAP初期化・スクロール演出)
  - form.js (フォーム制御)
  - instagram-gallery.js (Vue.js使用)
  - vue.min.js 2.6.14
```

### C-7. フッター構造

```
- ロゴ画像
- ナビゲーション8項目: コンセプト, よくあるご質問, サービスの特徴, 関連施設, サポート体制について, 運営会社, 代表挨拶, お問い合わせ
- SNSリンク2件（アイコンのみ、テキストなし）
- コピーライト: ©2025 koureishashoukai.com
```

### C-8. FAQ 全6件の質問文

```
Q1: 紹介料や相談料はかかりますか？
Q2: どんな施設を紹介してもらえますか？
Q3: 遠方に住んでいても相談できますか？
Q4: 医療処置が必要な親でも大丈夫ですか？
Q5: 入居後に合わなかった場合は？
Q6: 生活保護の相談できますか？
```

### C-9. pridセクション横流れ英字テキスト

```
"Medical and nursing care professionals help you achieve a "move-in without regrets.""
→ 2行で同じ文が繰り返される（infinite marquee用）
```

### C-10. メニュー（サポート体制）セクション 3件

```
#01: 残置物処理・引越代行
#02: 不動産サポート
#03: 手続き・制度サポート
```

### C-11. pride セクション内部構造

```
div.pride-points
  └ div.wp-block-columns.pride-point.js-pride-point-fx  (×3件)
      ├ div.wp-block-column.pride-point__content-block.has-custom-color-1-background-color
      │   └ div.pride-point-content
      │       ├ h3.wp-block-heading
      │       └ p（本文）
      └ div.wp-block-column.pride-point__fig-block
          └ div.wp-block-cover
              ├ span.wp-block-cover__background（背景色オーバーレイ）
              └ img
```
→ Gutenbergの `wp-block-columns` + `wp-block-cover` を活用した構造。
仕様書の「ページビルダーは使わない」は正確だが、**Gutenbergブロックを構造的に活用**している点は注記すべき。

### C-12. OGP/meta実測

```
og:title:       老人ホーム紹介コーディネーター
og:description:  看護師・介護士の専門チームが、ご家族の想い・持病・生活スタイルを丁寧にヒアリング。見学から契約、引っ越しまで一貫サポート。入居後の保証制度も充実し、初めての施設選びも安心です。
og:type:         website
og:image:        https://koureishashoukai.com/wp-content/uploads/sites/125/2026/03/ogp.jpg
viewport:        width=device-width, initial-scale=1
format-detection: telephone=no
```

---

## D. 仕様書の記述で確認が取れたもの（正確）

- ✅ セクションID一覧 (#mv, #concept, #pride, #menu, #greeting, #qa, #facility, #shop-info, #contact)
- ✅ フォント: 游ゴシック + Montserrat + EB Garamond
- ✅ Swiper 8 fade + GSAP 3.12.7
- ✅ CTA border-radius: 50px / 220px × 64px
- ✅ ヒーロー見出し 48px/96px/10px/700
- ✅ pridセクション3件構成
- ✅ FAQ アコーディオン形式
- ✅ フォーム: name, email, tel, message + honeypot
- ✅ カスタムフォーム実装（Contact Form 7不使用）
- ✅ desktop-first設計
- ✅ 基本3breakpoint (767/768-1023/1024)

---

## 検証環境

```
ツール:     agent-browser v0.20.5
エンジン:   Chrome 146.0.7680.80 (headless)
実行日:     2026-03-15
対象URL:    https://koureishashoukai.com/
```
