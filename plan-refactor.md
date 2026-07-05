# Refactoring Plan: lian-heart-web

## 現状
- Next.js 16 + TypeScript のLP（介護施設紹介サービス）
- page.tsx: 483行の巨大モノリシックコンポーネント
- page.module.css: 884行（上限800行超過）
- route.ts: 102行のフォーム送信API

## レビューで発見された問題（13件）

### CRITICAL
- C-1: CSRF保護なし（POST /api/contact）
- C-2: レート制限なし

### HIGH
- H-1: 483行のモノリシックコンポーネント → コンポーネント分割
- H-2: 884行のCSSファイル → CSS分割
- H-3: dangerouslySetInnerHTML でインラインJS注入（revealスクリプト）
- H-4: window.__lianHeartRevealInit グローバル汚染
- H-5: CTAボタン描画ロジックの重複（2箇所）
- H-6: font: inherit が font-size/line-height を上書き（CSS bug）

### MEDIUM
- M-1: CSS Module内でグローバルセレクタ使用（[data-reveal], #concept等）
- M-2: 不要な型キャスト（as ContactField）
- M-3: ハードコード色値 #ffffff → var(--light-text)
- M-4: フォーム送信中のフィードバックなし
- M-5: テストなし

## リファクタリング計画

### Phase 1: 安全な構造改善（体裁・機能に影響なし）
1. page.tsx をセクション別コンポーネントに分割
   - components/Header.tsx
   - components/Hero.tsx
   - components/Section.tsx (汎用)
   - components/CtaButtons.tsx (重複排除)
   - components/FaqList.tsx
   - components/ContactForm.tsx (Client Component)
   - components/RevealScript.tsx (Client Component)
   - components/Footer.tsx
2. page.module.css をコンポーネント別CSSに分割
3. content.ts の型定義改善（不要なasキャスト排除）

### Phase 2: バグ修正（体裁改善）
4. font: inherit → font-family: inherit に修正
5. #ffffff → var(--light-text) に統一

### Phase 3: セキュリティ改善
6. CSRF: Originヘッダー検証をroute.tsに追加
7. レート制限: 簡易IP制限 or Vercel Edge設定

## リスク評価
- Phase 1: コンポーネント分割は純粋なリファクタ。HTML出力が変わらなければ体裁・機能への影響なし
- Phase 2: CSSバグ修正なので体裁が「改善」する方向
- Phase 3: セキュリティ強化のみ、UI変更なし

## 検証方法
- npm run build でビルド成功確認
- npm run typecheck で型チェック
- ローカルdev確認（見た目・フォーム送信）
