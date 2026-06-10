<?php
/**
 * wp-config.php に追記するサンプル
 */

define('WP_ENVIRONMENT_TYPE', 'production');
define('LH_GAS_WEB_APP_URL', 'https://script.google.com/macros/s/REPLACE_ME/exec');

// 公開準備が整うまで noindex を強制する（検索エンジン非掲載を維持）。
// 電話・メール・住所などの実データ確定後に公開するときは、true→false に変更するか
// この行を削除する。それだけで robots メタが index,follow に切り替わり、
// JSON-LD と robots.txt の AI クローラ方針も自動的に有効化される。
define('LH_FORCE_NOINDEX', true);
