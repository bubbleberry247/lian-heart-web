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
        'フリガナ: ' . $payload['furigana'],
        '電話番号: ' . $payload['phone'],
        'メールアドレス: ' . $payload['email'],
        'ご本人との関係: ' . $payload['relationship'],
        '希望エリア: ' . $payload['area'],
        '希望する施設の種類: ' . $payload['facility_type'],
        '介護度: ' . $payload['care_level'],
        'ご予算: ' . $payload['budget'],
        '入居希望時期: ' . $payload['move_in_date'],
        'プライバシーポリシー同意: ' . ($payload['privacy'] === '1' ? '同意済み' : '未同意'),
        '送信元URL: ' . $payload['source_url'],
        'ユーザーエージェント: ' . $payload['user_agent'],
        '',
        '入居相談内容:',
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

    $payload = array(
        'name'          => sanitize_text_field($params['name'] ?? ''),
        'furigana'      => sanitize_text_field($params['furigana'] ?? ''),
        'phone'         => sanitize_text_field($params['phone'] ?? ''),
        'email'         => sanitize_email($params['email'] ?? ''),
        'relationship'  => sanitize_text_field($params['relationship'] ?? ''),
        'area'          => sanitize_text_field($params['area'] ?? ''),
        'facility_type' => sanitize_text_field($params['facility_type'] ?? ''),
        'care_level'    => sanitize_text_field($params['care_level'] ?? ''),
        'budget'        => sanitize_text_field($params['budget'] ?? ''),
        'move_in_date'  => sanitize_text_field($params['move_in_date'] ?? ''),
        'message'       => sanitize_textarea_field($params['message'] ?? ''),
        'privacy'       => !empty($params['privacy']) ? '1' : '0',
        'source_url'    => esc_url_raw($params['source_url'] ?? home_url('/')),
        'user_agent'    => sanitize_text_field($request->get_header('user_agent')),
        'submitted_at'  => current_time('c'),
    );

    if ($payload['name'] === '' || $payload['phone'] === '' || $payload['privacy'] !== '1') {
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
        return new WP_Error(
            'lh_contact_delivery_failed',
            '送信に失敗しました。',
            array('status' => 502)
        );
    }

    return rest_ensure_response(array(
        'message' => '送信ありがとうございました。内容を確認のうえご連絡いたします。',
    ));
}
