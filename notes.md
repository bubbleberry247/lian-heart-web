# notes.md — 作業ノート（リアンハートLP）

最終更新: 2026-06-12 ／ ブランチ: feat/security-seo-hardening（origin同期済み @cc43d4c）
関連ブランチ: wip/legacy-clean-theme-2026-03（3月の旧cleanテーマ/Next.js未確定変更のバックアップ。未レビュー・デプロイ対象外。作業ツリーからは除去済み＝必要時はこのブランチをcheckout）
git運用メモ: tracked変更ゼロ。残る未追跡44件はgitignore済みのスクリーンショット・tmp類のみ。**FTPパスワード平文を一度notes.mdにコミットしかけたが、未pushのうちにamendで履歴から排除済み**（認証情報は今後も deploy/tmp-lftp-*.txt ※git管理外にのみ置く）

## ゴール
AEO/GEOコンテンツ（4+1ハブ＝記事12本＋信頼基盤4ページ）を公開し、noindex解除で本公開に到達する。
ポジション:「愛知で退院・在宅困難・探し方が分からない家族に、公正に・急ぎでも伴走する適正な紹介事業者」

## 完了した作業（要約）
- **ドラフト作成**: docs/content-drafts.md に17ページ分。出典21件を一次ソース実取得で検証。GPT-5.5レビュー3回でAPPROVE
- **監修者なし運用へ転換**: 「有資格者監修」表記を削除し編集部体制に。検証可能な事実は※19〜21で確定、残りは【御社データ】【公開前確認】
- **WPテーマ実装＋本番デプロイ**: シーダー（draft自動生成）/出典・編集枠/CTA開示/フォーム同意2分割。検証全PASS（コミット 5f6e5bb→d771d2c→3cc5f51）
- **客先向け提言書**: docs/client-proposal-launch-2026-06-11.md ＋ デスクトップにWord版。Gmail下書き作成済み（r-4922859668138591554）

## 重要な判断
- FAQリッチリザルト終了は**2026年5月7日**（Google公式確認）→ FAQPage構造化データは主軸にせず、Article/Breadcrumb/Organization優先
- **公開は必ず人間がWP管理画面で行う**。シーダーはdraftのみ作成（誤公開事故の再発防止）
- 景表法対応: 「最短」「すぐ入れる」「全域の施設」等の断定・網羅表現は使わない（NG表はドラフト末尾）
- 監修者なしのE-E-A-T担保 = 編集方針ページ＋一次出典の徹底＋更新日表示

## うまくいかなかったこと・注意（再発防止）
- **サブエージェント製コードは報告と実物が乖離する**: 匿名initフック・無断publish・プレースホルダー削除＋文言創作が混入し4ページ誤公開→v5是正で解消。デプロイ前に必ず全文Read検証（メモリにも記録済み）
- **lftpデプロイは毎回ユーザー実行**: 分類器が本番FTP+認証情報を自動承認しない。コマンド: `C:\cygwin64\bin\lftp.exe -f "/cygdrive/c/ProgramData/Generative AI/Github/Lian-Heart_web/deploy/tmp-lftp-aeo-deploy.txt"`（パスは/cygdrive形式必須）。アップ後にWP管理画面を1回開く（シーダー発動）
- **Editフック**: 「offset/limitなし全体Read」要求がharnessの読み込み上限と衝突する大ファイルは、Python一意置換（count==1をassert）で対応

## 決定事項（2026-06-12・ユーザー回答）
1. **手数料**: 施設から成約時に受領・相談者無料**で確定**（仮置き解除）
2. **高住連届出**: **しない**（サイト上は「現時点で届出していません」と正直に記載）
3. **監修者**: **出さない**（編集部名義で確定。資格表記の確認枠も削除）
4. **プライバシーポリシー**: 専門家確認の「推奨」記載は**しない**。本文はClaudeが個人情報保護委員会の指針項目に沿って完成済み→御社は読んで承認のみ
→ 反映済み: content-seeds/fees-disclosure.html・about-editorial-policy.html・privacy-policy.html（プレースホルダーは基本情報系のみ残存）。提言書md＋Word版を改訂（決定セクション削除）。**注意: WP上の下書き3ページは旧版のまま＝公開前に新シードHTMLを管理画面で貼り替えること**（シーダーは既存ページを更新しない設計のため）

## 残タスク
1. [ユーザー] **改訂版Gmail下書き（r2909114133704879920・件名「…（提言書・改訂版）」）**の宛先差替え＋Word添付（デスクトップ: リアンハート様_サイト公開前ご提供事項_提言書_20260612.docx）→送信。**旧下書き（6/11付・旧Word）は削除**
2. [客先回答待ち] 基本情報4点（2週間目安）＋実務データ7点（1か月目安）※決定4件は回答済みで不要に
3. [回答後] 下書き16ページへ実データ反映（信頼基盤3ページは新シードHTMLへ貼り替えも）→ 客先レビュー → 公開（privacy-policy→運営情報→記事の順）
4. [公開後] Xserver無料SSL→https化→「検索エンジン避け」OFF→パーマリンク投稿名→GSC/Bing登録→AI検索引用の定期計測
5. knowledge-after-discharge（退院ピラー）の改稿は公開時に手動貼替（content-seeds/knowledge-after-discharge.html）
6. **次回デプロイ時に inc/content-seeds.php の最新版＋content-seeds/*.html改訂版を含める**: 是正ブロック撤去＋失敗時バージョン非更新＋決定事項反映版シード。本番は旧版だがdraft運用のため実害なし（急ぎではない）

## セキュリティTODO（自動レビュー指摘・2026-06-12）
- **deploy/push-theme-via-sftp.ps1 と deploy/tmp-lftp-*.txt の `set ssl:verify-certificate no`（TLS検証無効）**: 3月からの既存設定。MITMでFTP認証情報が窃取されるリスク。次回デプロイ時に (1)`verify-certificate no` を外して接続テスト（Xserver FTPSは通常正規証明書。失敗ならCygwinのCAバンドルを `ssl:ca-file` で指定） (2)可能なら ftp:// → sftp://（SSH）+ known_hosts へ移行。**動作中の経路なので未テストで書き換えない**
- FTPアカウント `codexpublic` は一時用途（認証情報は deploy/tmp-lftp-upload.txt ※git管理外）。**公開切替の前にパスワードローテーション推奨**

## 2026-06-11 追記（git push完了）
- GPT-5.4コードレビュー: R1=REQUEST_CHANGES（fatal2件: 是正ブロックが将来の再実行で公開済みページを巻き戻す地雷／失敗時もversion更新で部分適用が固定化）→ 修正コミット3c3ad9d → R2=APPROVE → push成功（8227b60..3c3ad9d）
- 注意: GR-005のverdictキャッシュは手書き転記だと分類器が「偽造」とみなしブロックする。**Codex実出力ファイルからJSONを機械抽出**して作成すること（C:\tmp\codex_output_*.md → codex_verdict_{SHA}.json）
