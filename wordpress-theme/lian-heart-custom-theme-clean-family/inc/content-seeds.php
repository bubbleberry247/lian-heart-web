<?php
if (!defined('ABSPATH')) {
    exit;
}

define('LH_CONTENT_SEEDS_VERSION', '5');

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

    // v5一回限りの是正対象: シーダーv3が誤って公開し、プレースホルダーを欠いた
    // 本文で作成してしまった信頼基盤4ページ。下書きへ戻し、本文を正本シード
    // （プレースホルダー付き）へ置き換える。公開判断は必ず人間が行う。
    $corrective_slugs = array(
        'about-editorial-policy',
        'fees-disclosure',
        'consultation-policy',
        'privacy-policy',
    );

    foreach ($manifest as $slug => $entry) {
        if (!is_string($slug) || $slug === '' || !is_array($entry)) {
            continue;
        }

        $html_path = $seed_dir . $slug . '.html';
        $existing_page = get_page_by_path($slug);

        if ($existing_page instanceof WP_Post) {
            if (in_array($slug, $corrective_slugs, true)) {
                $update = array(
                    'ID'          => (int) $existing_page->ID,
                    'post_status' => 'draft',
                );

                if (file_exists($html_path)) {
                    $replacement = file_get_contents($html_path);
                    if (is_string($replacement) && trim($replacement) !== '') {
                        $update['post_content'] = $replacement;
                    }
                }

                wp_update_post($update, true);
                lh_apply_content_seed_meta((int) $existing_page->ID, $entry);
            }
            // 是正対象以外の既存ページは内容・状態・メタとも一切変更しない。
            continue;
        }
        if (!file_exists($html_path)) {
            continue;
        }

        $content = file_get_contents($html_path);
        if (!is_string($content) || trim($content) === '') {
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
            continue;
        }

        $page_id = (int) $page_id;

        if (($entry['type'] ?? '') === 'knowledge') {
            update_post_meta($page_id, '_wp_page_template', 'page-templates/template-knowledge-article.php');
        }

        lh_apply_content_seed_meta($page_id, $entry);
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
