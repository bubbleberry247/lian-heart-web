<?php
if (!defined('ABSPATH')) {
    exit;
}

define('LH_CONTENT_SEEDS_VERSION', '7');

function lh_content_seed_manifest() {
    $manifest_path = get_template_directory() . '/content-seeds/manifest.json';
    if (!file_exists($manifest_path)) {
        return array();
    }

    $json = file_get_contents($manifest_path);
    if (!is_string($json) || $json === '') {
        return array();
    }

    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        return array();
    }

    return $decoded;
}

function lh_apply_content_seed_meta($page_id, array $entry) {
    $meta_map = array(
        'lh_article_en_label'         => (string) ($entry['en_label'] ?? ''),
        'lh_article_lead'             => (string) ($entry['lead'] ?? ''),
        'lh_article_cta_title'        => (string) ($entry['cta_title'] ?? ''),
        'lh_article_cta_body'         => (string) ($entry['cta_body'] ?? ''),
        'lh_article_meta_description' => (string) ($entry['meta_description'] ?? ''),
    );

    $sources = $entry['sources'] ?? array();
    if (is_array($sources)) {
        $meta_map['lh_article_sources'] = implode("\n", array_values(array_filter(array_map('strval', $sources))));
    }

    foreach ($meta_map as $meta_key => $meta_value) {
        if ($meta_value === '') {
            continue;
        }

        update_post_meta((int) $page_id, $meta_key, $meta_value);
    }
}

function lh_ensure_content_pages() {
    $applied_version = (string) get_option('lh_content_seeds_version', '0');
    if (version_compare($applied_version, LH_CONTENT_SEEDS_VERSION, '>=')) {
        return;
    }

    $manifest = lh_content_seed_manifest();
    if ($manifest === array()) {
        return;
    }

    $seed_dir = trailingslashit(get_template_directory()) . 'content-seeds/';
    $had_failure = false;

    foreach ($manifest as $slug => $entry) {
        if (!is_string($slug) || $slug === '' || !is_array($entry)) {
            continue;
        }

        // 既存ページ（slug一致）は内容・状態・メタとも一切変更しない。
        // 公開・改稿の判断は必ず人間がWP管理画面で行う。
        if (get_page_by_path($slug) instanceof WP_Post) {
            continue;
        }

        $html_path = $seed_dir . $slug . '.html';
        if (!file_exists($html_path)) {
            error_log('[LH Seeds] missing seed html: ' . $slug);
            $had_failure = true;
            continue;
        }

        $content = file_get_contents($html_path);
        if (!is_string($content) || trim($content) === '') {
            error_log('[LH Seeds] unreadable/empty seed html: ' . $slug);
            $had_failure = true;
            continue;
        }

        // シーダーが作成するページは常に draft。公開はWP管理画面で人間が行う。
        $page_id = wp_insert_post(
            array(
                'post_type'      => 'page',
                'post_status'    => 'draft',
                'post_title'     => (string) ($entry['title'] ?? $slug),
                'post_name'      => $slug,
                'post_content'   => $content,
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            ),
            true
        );

        if (is_wp_error($page_id) || (int) $page_id <= 0) {
            error_log('[LH Seeds] wp_insert_post failed: ' . $slug . ' ' . (is_wp_error($page_id) ? $page_id->get_error_message() : ''));
            $had_failure = true;
            continue;
        }

        $page_id = (int) $page_id;

        if (($entry['type'] ?? '') === 'knowledge') {
            update_post_meta($page_id, '_wp_page_template', 'page-templates/template-knowledge-article.php');
        }

        lh_apply_content_seed_meta($page_id, $entry);
    }

    // 1件でも失敗したら適用済みにしない（次回のadmin_initで再試行される）。
    if ($had_failure) {
        error_log('[LH Seeds] partial failure - version not advanced (will retry)');
        return;
    }

    update_option('lh_content_seeds_version', LH_CONTENT_SEEDS_VERSION);
}
add_action('after_switch_theme', 'lh_ensure_content_pages');

function lh_ensure_content_pages_admin() {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    lh_ensure_content_pages();
}
add_action('admin_init', 'lh_ensure_content_pages_admin');
