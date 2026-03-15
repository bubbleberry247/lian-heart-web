<?php
if (!defined('ABSPATH')) {
    exit;
}

function lh_acf_text($key, $label, $name) {
    return array(
        'key'   => $key,
        'label' => $label,
        'name'  => $name,
        'type'  => 'text',
    );
}

function lh_acf_textarea($key, $label, $name, $rows = 4) {
    return array(
        'key'   => $key,
        'label' => $label,
        'name'  => $name,
        'type'  => 'textarea',
        'rows'  => $rows,
        'new_lines' => 'br',
    );
}

function lh_acf_image($key, $label, $name) {
    return array(
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'image',
        'return_format' => 'array',
        'preview_size'  => 'medium',
        'library'       => 'all',
    );
}

function lh_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_lh_theme_options',
        'title' => 'Lian Heart Theme Settings',
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'lian-heart-theme-settings',
                ),
            ),
        ),
        'style' => 'seamless',
        'fields' => array(
            array(
                'key' => 'field_lh_brand',
                'label' => 'ブランド',
                'name' => 'lh_brand',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_brand_site_name', 'サイト名', 'site_name'),
                    lh_acf_text('field_lh_brand_tagline', 'タグライン', 'tagline'),
                    lh_acf_image('field_lh_brand_logo', 'ロゴ', 'logo'),
                    lh_acf_textarea('field_lh_brand_footer_note', 'フッター補足', 'footer_note', 3),
                    lh_acf_text('field_lh_brand_copyright', 'コピーライト', 'copyright'),
                ),
            ),
            array(
                'key' => 'field_lh_hero',
                'label' => 'ヒーロー',
                'name' => 'lh_hero',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_hero_eyebrow', 'アイブロウ', 'eyebrow'),
                    lh_acf_textarea('field_lh_hero_title', 'タイトル', 'title', 5),
                    lh_acf_textarea('field_lh_hero_description', '説明文', 'description', 5),
                    array(
                        'key' => 'field_lh_hero_slides',
                        'label' => 'スライド',
                        'name' => 'slides',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'スライドを追加',
                        'sub_fields' => array(
                            lh_acf_image('field_lh_hero_slide_desktop', 'Desktop画像', 'desktop_image'),
                            lh_acf_image('field_lh_hero_slide_mobile', 'Mobile画像', 'mobile_image'),
                            lh_acf_text('field_lh_hero_slide_alt', '代替テキスト', 'alt'),
                        ),
                    ),
                    array(
                        'key' => 'field_lh_hero_ctas',
                        'label' => 'CTA',
                        'name' => 'ctas',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'CTAを追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_hero_cta_label', 'ラベル', 'label'),
                            lh_acf_text('field_lh_hero_cta_url', 'URL', 'url'),
                            array(
                                'key' => 'field_lh_hero_cta_style',
                                'label' => 'スタイル',
                                'name' => 'style',
                                'type' => 'select',
                                'choices' => array(
                                    'primary' => 'Primary',
                                    'line' => 'LINE',
                                    'ghost' => 'Ghost',
                                ),
                                'default_value' => 'primary',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_concept',
                'label' => 'コンセプト',
                'name' => 'lh_concept',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_concept_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_concept_title', 'タイトル', 'title'),
                    lh_acf_textarea('field_lh_concept_lead', 'リード', 'lead', 3),
                    array(
                        'key' => 'field_lh_concept_body',
                        'label' => '本文',
                        'name' => 'body',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '段落を追加',
                        'sub_fields' => array(
                            lh_acf_textarea('field_lh_concept_body_text', '本文', 'text', 3),
                        ),
                    ),
                    array(
                        'key' => 'field_lh_concept_visuals',
                        'label' => '画像',
                        'name' => 'visuals',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '画像を追加',
                        'sub_fields' => array(
                            lh_acf_image('field_lh_concept_visual_image', '画像', 'image'),
                            lh_acf_text('field_lh_concept_visual_alt', '代替テキスト', 'alt'),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_pride',
                'label' => 'サービスの特徴',
                'name' => 'lh_pride',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_pride_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_pride_title', 'タイトル', 'title'),
                    lh_acf_text('field_lh_pride_rail_text', '横流れテキスト', 'rail_text'),
                    array(
                        'key' => 'field_lh_pride_cards',
                        'label' => '項目',
                        'name' => 'cards',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => '項目を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_pride_card_code', 'コード', 'code'),
                            lh_acf_text('field_lh_pride_card_title', '見出し', 'title'),
                            lh_acf_textarea('field_lh_pride_card_body', '本文', 'body', 6),
                            lh_acf_image('field_lh_pride_card_image', '画像', 'image'),
                        ),
                    ),
                    lh_acf_text('field_lh_pride_side_title', '補助タイトル', 'side_title'),
                    lh_acf_textarea('field_lh_pride_side_body', '補助本文', 'side_body', 4),
                    lh_acf_image('field_lh_pride_side_image', '補助画像', 'side_image'),
                ),
            ),
            array(
                'key' => 'field_lh_menu',
                'label' => '流れ',
                'name' => 'lh_menu',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_menu_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_menu_title', 'タイトル', 'title'),
                    array(
                        'key' => 'field_lh_menu_cards',
                        'label' => '項目',
                        'name' => 'cards',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => '項目を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_menu_card_code', 'コード', 'code'),
                            lh_acf_text('field_lh_menu_card_title', '見出し', 'title'),
                            lh_acf_textarea('field_lh_menu_card_body', '本文', 'body', 6),
                            lh_acf_image('field_lh_menu_card_image', '画像', 'image'),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_greeting',
                'label' => '代表挨拶',
                'name' => 'lh_greeting',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_greeting_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_greeting_title', 'タイトル', 'title'),
                    lh_acf_text('field_lh_greeting_name', '氏名', 'name'),
                    lh_acf_text('field_lh_greeting_role', '役職', 'role'),
                    array(
                        'key' => 'field_lh_greeting_body',
                        'label' => '本文',
                        'name' => 'body',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '段落を追加',
                        'sub_fields' => array(
                            lh_acf_textarea('field_lh_greeting_body_text', '本文', 'text', 3),
                        ),
                    ),
                    lh_acf_image('field_lh_greeting_image', '画像', 'image'),
                    lh_acf_text('field_lh_greeting_decoration', '装飾英字', 'decoration'),
                ),
            ),
            array(
                'key' => 'field_lh_qa',
                'label' => 'FAQ',
                'name' => 'lh_qa',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_qa_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_qa_title', 'タイトル', 'title'),
                    array(
                        'key' => 'field_lh_qa_items',
                        'label' => '質問',
                        'name' => 'items',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => '質問を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_qa_question', '質問', 'question'),
                            lh_acf_textarea('field_lh_qa_answer', '回答', 'answer', 4),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_facility',
                'label' => '施設の種類',
                'name' => 'lh_facility',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_facility_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_facility_title', 'タイトル', 'title'),
                    lh_acf_textarea('field_lh_facility_lead', 'リード', 'lead', 3),
                    array(
                        'key' => 'field_lh_facility_items',
                        'label' => '施設項目',
                        'name' => 'items',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => '施設を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_facility_item_title', 'タイトル', 'title'),
                            lh_acf_textarea('field_lh_facility_item_description', '説明', 'description', 4),
                            lh_acf_image('field_lh_facility_item_image', '画像', 'image'),
                            lh_acf_text('field_lh_facility_item_url', 'URL', 'url'),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_company',
                'label' => '運営会社',
                'name' => 'lh_company',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_company_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_company_title', 'タイトル', 'title'),
                    lh_acf_image('field_lh_company_visual', '左ビジュアル', 'visual'),
                    array(
                        'key' => 'field_lh_company_rows',
                        'label' => '会社情報',
                        'name' => 'rows',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '行を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_company_row_label', '項目名', 'label'),
                            lh_acf_textarea('field_lh_company_row_value', '値', 'value', 2),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_lh_contact',
                'label' => 'お問い合わせ',
                'name' => 'lh_contact',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    lh_acf_text('field_lh_contact_en_label', '英字ラベル', 'en_label'),
                    lh_acf_text('field_lh_contact_title', 'タイトル', 'title'),
                    lh_acf_textarea('field_lh_contact_catch', 'キャッチ', 'catch', 3),
                    lh_acf_text('field_lh_contact_lead_title', '左カラム見出し', 'lead_title'),
                    array(
                        'key' => 'field_lh_contact_lead_body',
                        'label' => '左カラム本文',
                        'name' => 'lead_body',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '段落を追加',
                        'sub_fields' => array(
                            lh_acf_textarea('field_lh_contact_lead_body_text', '本文', 'text', 3),
                        ),
                    ),
                    array(
                        'key' => 'field_lh_contact_notes',
                        'label' => '注意文',
                        'name' => 'notes',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => '注意文を追加',
                        'sub_fields' => array(
                            lh_acf_text('field_lh_contact_note_text', '本文', 'text'),
                        ),
                    ),
                    lh_acf_text('field_lh_contact_form_title', 'フォーム見出し', 'form_title'),
                    lh_acf_text('field_lh_contact_success_title', '完了見出し', 'success_title'),
                    lh_acf_textarea('field_lh_contact_success_body', '完了本文', 'success_body', 3),
                    lh_acf_text('field_lh_contact_recipient_email', '通知先メールアドレス', 'recipient_email'),
                ),
            ),
        ),
    ));
}
add_action('acf/init', 'lh_register_acf_fields');

