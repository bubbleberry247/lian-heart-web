<?php
if (!defined('ABSPATH')) {
    exit;
}

function lh_contact_recipient_emails() {
    $contact = lh_get_option_group('contact');
    $emails  = array();

    if (is_array($contact) && !empty($contact['recipient_email'])) {
        $emails[] = sanitize_email($contact['recipient_email']);
    }

    $admin_email = sanitize_email((string) get_option('admin_email'));
    if ($admin_email !== '') {
        $emails[] = $admin_email;
    }

    $emails = array_values(array_unique(array_filter($emails, 'is_email')));
    return $emails;
}

function lh_contact_mail_subject(array $payload) {
    $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $name      = $payload['name'] !== '' ? $payload['name'] : 'お名前未入力';

    return sprintf('【%s】入居相談フォーム送信 - %s', $site_name, $name);
}

function lh_contact_mail_body(array $payload) {
    $lines = array(
        '入居相談フォームから送信がありました。',
        '',
        '送信日時: ' . $payload['submitted_at'],
        'お名前: ' . $payload['name'],
        'メールアドレス: ' . $payload['email'],
        '電話番号: ' . $payload['phone'],
        'プライバシーポリシー同意: ' . ($payload['privacy'] === '1' ? '同意済み' : '未同意'),
        '送信元URL: ' . $payload['source_url'],
        'ユーザーエージェント: ' . $payload['user_agent'],
        '',
        'お問い合わせ内容:',
        $payload['message'] !== '' ? $payload['message'] : '（未入力）',
    );

    return implode("\n", $lines);
}

function lh_register_contact_route() {
    register_rest_route(
        'lian-heart/v1',
        '/contact',
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'permission_callback' => '__return_true',
            'callback'            => 'lh_handle_contact_submission',
        )
    );
}
add_action('rest_api_init', 'lh_register_contact_route');

function lh_handle_contact_submission(WP_REST_Request $request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    if (!empty($params['website'])) {
        return rest_ensure_response(array('message' => 'ok'));
    }

    // --- Submission ID ---
    $submission_id = date('Ymd-His') . '-' . wp_rand(1000, 9999);

    // --- Rate limiting (transient-based, 3 requests / 60 seconds per IP) ---
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rate_key  = 'lh_rate_' . md5($client_ip);
    $attempts  = (int) get_transient($rate_key);

    if ($attempts >= 3) {
        error_log(sprintf('[LH Contact] RATE_LIMITED ip=%s attempts=%d', $client_ip, $attempts));
        return new WP_Error(
            'lh_rate_limited',
            '送信回数の上限に達しました。しばらくしてからお試しください。',
            array('status' => 429)
        );
    }

    set_transient($rate_key, $attempts + 1, 60);

    $payload = array(
        'name'          => sanitize_text_field($params['name'] ?? ''),
        'email'         => sanitize_email($params['email'] ?? ''),
        'phone'         => sanitize_text_field($params['phone'] ?? ''),
        'message'       => sanitize_textarea_field($params['message'] ?? ''),
        'privacy'       => !empty($params['privacy']) ? '1' : '0',
        'source_url'    => esc_url_raw($params['source_url'] ?? home_url('/')),
        'user_agent'    => sanitize_text_field($request->get_header('user_agent')),
        'submitted_at'  => current_time('c'),
    );

    if (
        $payload['name'] === '' ||
        $payload['email'] === '' ||
        !is_email($payload['email']) ||
        $payload['phone'] === '' ||
        $payload['message'] === '' ||
        $payload['privacy'] !== '1'
    ) {
        error_log(sprintf(
            '[LH Contact] REJECT id=%s ip=%s reason=validation_failed',
            $submission_id,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        return new WP_Error(
            'lh_invalid_contact',
            '必須項目を確認してください。',
            array('status' => 400)
        );
    }

    $recipients = lh_contact_recipient_emails();
    if (empty($recipients)) {
        return new WP_Error(
            'lh_missing_mail_recipient',
            '送信先メールアドレスが設定されていません。',
            array('status' => 500)
        );
    }

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    if ($payload['email'] !== '' && is_email($payload['email'])) {
        $reply_to_name = $payload['name'] !== '' ? $payload['name'] : 'お問い合わせ送信者';
        $headers[]     = sprintf('Reply-To: %s <%s>', $reply_to_name, $payload['email']);
    }

    $sent = wp_mail(
        $recipients,
        lh_contact_mail_subject($payload),
        lh_contact_mail_body($payload),
        $headers
    );

    if (!$sent) {
        error_log(sprintf(
            '[LH Contact] FAIL id=%s name=%s email=%s ip=%s reason=mail_delivery_failed',
            $submission_id,
            $payload['name'],
            $payload['email'],
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        return new WP_Error(
            'lh_contact_delivery_failed',
            '送信に失敗しました。',
            array('status' => 502)
        );
    }

    error_log(sprintf(
        '[LH Contact] OK id=%s name=%s email=%s ip=%s',
        $submission_id,
        $payload['name'],
        $payload['email'],
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));

    return rest_ensure_response(array(
        'message' => '送信ありがとうございました。内容を確認のうえご連絡いたします。',
    ));
}
