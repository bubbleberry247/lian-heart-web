<?php
if (!defined('ABSPATH')) {
    exit;
}

$lh_required_files = array(
    'theme-setup'      => get_template_directory() . '/inc/theme-setup.php',
    'acf-fields'       => get_template_directory() . '/inc/acf-fields.php',
    'rest-contact'     => get_template_directory() . '/inc/rest-contact.php',
    'fallback-options' => get_template_directory() . '/inc/fallback-options.php',
);

$lh_missing_files = array();

foreach ($lh_required_files as $lh_label => $lh_path) {
    if (file_exists($lh_path)) {
        require_once $lh_path;
        continue;
    }

    $lh_missing_files[$lh_label] = $lh_path;
}

if (!empty($lh_missing_files) && is_admin()) {
    add_action(
        'admin_notices',
        static function () use ($lh_missing_files) {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('Lian Heart Custom Theme の必須ファイルが不足しています。テーマを再アップロードしてください。', 'lian-heart-custom-theme');
            echo '</p><ul style="margin:8px 0 0 1.2em; list-style:disc;">';

            foreach ($lh_missing_files as $lh_label => $lh_path) {
                printf(
                    '<li><code>%s</code>: <code>%s</code></li>',
                    esc_html($lh_label),
                    esc_html($lh_path)
                );
            }

            echo '</ul></div>';
        }
    );
}
