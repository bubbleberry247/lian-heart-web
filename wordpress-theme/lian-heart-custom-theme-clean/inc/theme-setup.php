<?php
if (!defined('ABSPATH')) {
    exit;
}

function lh_theme_version() {
    $theme = wp_get_theme();
    return $theme->get('Version') ?: '1.0.0';
}

function lh_is_assoc(array $value) {
    return array_keys($value) !== range(0, count($value) - 1);
}

function lh_deep_merge($defaults, $overrides) {
    if (!is_array($defaults) || !is_array($overrides)) {
        return $overrides === null || $overrides === '' ? $defaults : $overrides;
    }

    if (!lh_is_assoc($defaults) || !lh_is_assoc($overrides)) {
        return !empty($overrides) ? $overrides : $defaults;
    }

    $merged = $defaults;

    foreach ($overrides as $key => $value) {
        if (!array_key_exists($key, $defaults)) {
            $merged[$key] = $value;
            continue;
        }

        $merged[$key] = lh_deep_merge($defaults[$key], $value);
    }

    return $merged;
}

function lh_merge_indexed_items($defaults, $overrides) {
    if (!is_array($defaults)) {
        return is_array($overrides) ? $overrides : $defaults;
    }

    if (!is_array($overrides) || $overrides === array()) {
        return $defaults;
    }

    $merged = array();
    $max = max(count($defaults), count($overrides));

    for ($i = 0; $i < $max; $i++) {
        $default_item = $defaults[$i] ?? array();
        $override_item = $overrides[$i] ?? array();

        if (is_array($default_item) && is_array($override_item)) {
            $merged[] = lh_deep_merge($default_item, $override_item);
            continue;
        }

        if ($override_item === null || $override_item === '') {
            $merged[] = $default_item;
            continue;
        }

        $merged[] = $override_item;
    }

    return $merged;
}

function lh_fill_empty($value, $fallback) {
    if ($value === null || $value === '') {
        return $fallback;
    }

    if (is_array($value) && $value === array()) {
        return $fallback;
    }

    return $value;
}

function lh_paragraphs($value) {
    if (is_array($value)) {
        return array_values(array_filter(array_map('trim', $value)));
    }

    if (!is_string($value) || trim($value) === '') {
        return array();
    }

    $parts = preg_split('/\r\n|\r|\n/', trim($value));
    return array_values(array_filter(array_map('trim', $parts)));
}

function lh_placeholder_image($label = 'Placeholder', $width = 1600, $height = 900, $background = 'fde8eb', $foreground = '0c1c1f') {
    $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d"><rect width="100%%" height="100%%" fill="#%3$s"/><text x="50%%" y="50%%" fill="#%4$s" font-family="Yu Gothic, YuGothic, sans-serif" font-size="64" font-weight="700" text-anchor="middle" dominant-baseline="middle">%5$s</text></svg>',
        $width,
        $height,
        $background,
        $foreground,
        esc_html($label)
    );

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

function lh_theme_asset_uri($relative_path) {
    return trailingslashit(get_template_directory_uri()) . ltrim($relative_path, '/');
}

function lh_resolve_image($image, $fallback = 'Placeholder', $width = 1600, $height = 900) {
    if (is_array($image) && !empty($image['url'])) {
        return array(
            'url' => $image['url'],
            'alt' => $image['alt'] ?? $fallback,
        );
    }

    if (is_string($image) && $image !== '') {
        return array(
            'url' => $image,
            'alt' => $fallback,
        );
    }

    return array(
        'url' => lh_placeholder_image($fallback, $width, $height),
        'alt' => $fallback,
    );
}

function lh_button_class($style = 'primary') {
    $map = array(
        'primary' => 'btn btn--primary',
        'line'    => 'btn btn--line',
        'ghost'   => 'btn btn--ghost',
    );

    return $map[$style] ?? $map['primary'];
}

function lh_resolve_anchor_url($url) {
    if (!is_string($url) || $url === '') {
        return home_url('/');
    }

    if (strpos($url, '#') !== 0) {
        return $url;
    }

    if (is_front_page()) {
        return $url;
    }

    return trailingslashit(home_url('/')) . $url;
}

function lh_get_cta_url($item) {
    if (!is_array($item)) {
        return lh_resolve_anchor_url('#contact');
    }

    if (!empty($item['url'])) {
        return lh_resolve_anchor_url($item['url']);
    }

    return lh_resolve_anchor_url('#contact');
}

function lh_knowledge_article_definitions() {
    return array(
        'knowledge-facility-choice' => array(
            'slug' => 'knowledge-facility-choice',
            'url' => home_url('/knowledge-facility-choice/'),
            'en_label' => 'Knowledge',
            'title' => '施設の選び方 介護付・住宅型・サ高住の違い',
            'hero_image' => lh_theme_asset_uri('assets/media/facility-type-01-v2.jpg'),
            'card_title' => '施設の選び方 介護付・住宅型・サ高住の違い',
            'card_body' => '介護付・住宅型・サ高住の違いと、それぞれどんな方に向きやすいかを整理します。',
            'lead' => '介護付有料老人ホーム、住宅型有料老人ホーム、サービス付き高齢者向け住宅は、似て見えても受けられる支援や暮らし方が異なります。特徴の違いを整理し、ご本人やご家族にとって無理のない選び方を考えます。',
            'cta_title' => '施設の違いを整理しながら相談したい方へ',
            'cta_body' => 'ご本人の状態や暮らし方、ご家族の通いやすさを踏まえながら、候補を比較しやすい形でご案内します。',
        ),
        'knowledge-family-checkpoints' => array(
            'slug' => 'knowledge-family-checkpoints',
            'url' => home_url('/knowledge-family-checkpoints/'),
            'en_label' => 'Knowledge',
            'title' => '家族が後悔しないための施設選び 何を基準に見るべきか',
            'hero_image' => lh_theme_asset_uri('assets/media/service-aside.jpg'),
            'card_title' => '家族が後悔しないための施設選び 何を基準に見るべきか',
            'card_body' => '面会しやすさ、医療対応、雰囲気など、家族が実際に見ている基準を整理します。',
            'lead' => '施設選びでは、料金や空室だけでなく、面会しやすさ、スタッフの雰囲気、医療対応、追加費用の見え方など、暮らし始めてからの安心につながる視点が欠かせません。家族が後悔しにくい見方を整理します。',
            'cta_title' => '家族目線で比較ポイントを整理したい方へ',
            'cta_body' => '見学前の確認項目づくりや、候補を比べる軸の整理から一緒に進められます。',
        ),
        'knowledge-after-discharge' => array(
            'slug' => 'knowledge-after-discharge',
            'url' => home_url('/knowledge-after-discharge/'),
            'en_label' => 'Knowledge',
            'title' => '退院後にあわてないための施設探し 何から決めるべきか',
            'hero_image' => lh_theme_asset_uri('assets/media/support-flow-01.jpg'),
            'card_title' => '退院後にあわてないための施設探し 何から決めるべきか',
            'card_body' => '退院前後の慌ただしい時期に、何を先に整理すべきかを分かりやすくまとめます。',
            'lead' => '退院が近づくと、本人の体調、在宅介護の負担、医療面の受け入れ、移動距離など、短い時間で確認すべきことが増えます。急ぎの状況でも見落としを減らすための順番を整理します。',
            'cta_title' => '退院前後の住まい探しを相談したい方へ',
            'cta_body' => '今すぐ決めるべきことと後から詰められることを分けながら、焦らず候補整理を進められます。',
        ),
    );
}

function lh_render_button($item, $extra_class = '') {
    if (!is_array($item) || empty($item['label'])) {
        return '';
    }

    $classes = trim(lh_button_class($item['style'] ?? 'primary') . ' ' . $extra_class);
    $url = lh_get_cta_url($item);

    return sprintf(
        '<a class="%1$s" href="%2$s"><span>%3$s</span><i class="btn__icon" aria-hidden="true"></i></a>',
        esc_attr($classes),
        esc_url($url),
        esc_html($item['label'])
    );
}

function lh_theme_defaults() {
    $knowledge_articles = array_values(lh_knowledge_article_definitions());

    return array(
        'brand' => array(
            'site_name'   => 'リアンハート',
            'tagline'     => '愛知県の老人ホーム紹介・入居相談',
            'logo'        => lh_theme_asset_uri('assets/media/logo.png'),
            'header_cta'  => array('label' => '入居相談', 'url' => '#contact', 'style' => 'primary'),
            'footer_note' => '見た目と動きは専用カスタムテーマで構成し、本文や会社情報はすべて差し替えできます。',
            'copyright'   => '© リアンハート All Rights Reserved.',
        ),
        'hero' => array(
            'eyebrow'     => '愛知県全域対応の老人ホーム紹介・入居相談',
            'title'       => "愛知で老人ホーム紹介を\nご希望の方へ。\n入居相談から比較まで伴走します。",
            'description' => "介護度・費用・立地・ご家族の通いやすさを整理しながら、最適な施設候補をご提案します。",
            'slides'      => array(
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-01-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-01-mobile.jpg'),
                    'alt'           => 'ヒーロー画像 01',
                ),
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-02-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-02-mobile.jpg'),
                    'alt'           => 'ヒーロー画像 02',
                ),
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-03-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-03-mobile.jpg'),
                    'alt'           => 'ヒーロー画像 03',
                ),
            ),
            'ctas' => array(
                array('label' => '電話で入居相談する', 'url' => 'tel:052-000-0000', 'style' => 'primary'),
                array('label' => 'LINEで入居相談する', 'url' => '#contact', 'style' => 'line'),
            ),
        ),
        'trust' => array(
            'items' => array(
                array('label' => 'Free', 'text' => '相談・ご紹介はすべて無料'),
                array('label' => 'Area', 'text' => '愛知県全域でご相談対応'),
                array('label' => 'Visit', 'text' => '見学調整や比較整理もサポート'),
                array('label' => 'Family', 'text' => 'ご家族だけのご相談も可能'),
            ),
        ),
        'timing' => array(
            'en_label' => 'Timing',
            'title'    => 'こんなときにご相談ください',
            'items'    => array(
                array(
                    'title' => '退院後の生活が不安',
                    'body'  => '医療面や生活動線も含めて、無理のない候補を整理したい方へ。',
                ),
                array(
                    'title' => '一人暮らしの不安が増えた',
                    'body'  => '見守り体制や生活支援の内容を比べながら検討したい方へ。',
                ),
                array(
                    'title' => '介護負担が大きくなってきた',
                    'body'  => 'ご家族だけで抱え込まず、候補整理から相談したい方へ。',
                ),
                array(
                    'title' => '元気なうちに住み替え準備をしたい',
                    'body'  => '早めに比較を始めて、将来に備えたい方へ。',
                ),
            ),
            'cta' => array('label' => '相談してみる', 'url' => '#contact', 'style' => 'primary'),
        ),
        'concept' => array(
            'en_label' => 'Concept',
            'title'    => "納得できる\n老人ホーム紹介を。",
            'lead'     => '介護施設紹介は、空室や料金だけで決められるものではありません。',
            'body'     => array(
                '施設選びは、費用や場所だけで決めきれません。医療対応や暮らしやすさまで、最初に整理することが大切です。',
                '急いで候補を絞るほど、「別もあったのでは」と迷いが残りやすくなります。だからこそ、比べる軸を先につくります。',
                'リアンハートでは、ご本人の状態やご家族の通いやすさを確かめながら、無理のない候補を一緒に整えていきます。',
                '見学前の整理から比較まで伴走し、納得できる入居相談につなげます。',
            ),
            'visuals'  => array(
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-01.jpg'), 'alt' => 'コンセプト画像 01'),
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-02.jpg'), 'alt' => 'コンセプト画像 02'),
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-03.jpg'), 'alt' => 'コンセプト画像 03'),
            ),
        ),
        'pride' => array(
            'en_label' => 'Service',
            'title'    => '介護施設紹介で大切にしている3つのこと',
            'rail_text' => 'Lian Heart Senior Living Support',
            'cards' => array(
                array(
                    'code'  => 'Service01',
                    'title' => '入居相談で条件を整理する',
                    'body'  => "介護度、医療的な配慮、費用、希望エリア、生活スタイルなどを確認し、ご状況に合う候補を整理します。\nご家族の通いやすさや暮らし方まで見ながら、比較の軸を明確にします。",
                    'image' => lh_theme_asset_uri('assets/media/service-feature-01.jpg'),
                ),
                array(
                    'code'  => 'Service02',
                    'title' => '老人ホーム紹介を比較しやすくする',
                    'body'  => "候補施設の見学調整だけでなく、確認すべきポイントを整理し、複数施設を比較しやすい状態で検討できるよう支援します。\n急がせず、納得できる選び方を前提に進めます。",
                    'image' => lh_theme_asset_uri('assets/media/service-feature-02.jpg'),
                ),
                array(
                    'code'  => 'Service03',
                    'title' => '介護施設紹介でミスマッチを減らす',
                    'body'  => "設備や費用だけでなく、暮らし方や支援体制との相性まで事前に確認し、入居後のギャップを減らします。\n必要な配慮事項を事前にすり合わせながら候補を絞ります。",
                    'image' => lh_theme_asset_uri('assets/media/service-feature-03.jpg'),
                ),
            ),
            'side_title' => '比較しやすい状態を先につくる',
            'side_body'  => '空室の有無だけでなく、介護度、医療的配慮、費用帯、立地、家族動線まで整理したうえで候補をご提案する設計です。',
            'side_image' => lh_theme_asset_uri('assets/media/service-aside.jpg'),
        ),
        'menu' => array(
            'en_label' => 'Flow',
            'title'    => '入居相談から老人ホーム紹介までの流れ',
            'cards'    => array(
                array(
                    'code'  => 'Flow01',
                    'title' => '入居相談',
                    'body'  => "現在の生活状況、希望エリア、予算、入居時期、ご家族のご希望などを整理します。\n未定の項目があっても、確認すべき順番からご案内します。",
                    'image' => lh_theme_asset_uri('assets/media/support-flow-01.jpg'),
                ),
                array(
                    'code'  => 'Flow02',
                    'title' => '老人ホーム紹介',
                    'body'  => "整理した条件をもとに、検討しやすい候補をご案内します。\n受入条件や生活イメージも合わせて比較しやすく整えます。",
                    'image' => lh_theme_asset_uri('assets/media/support-flow-02.png'),
                ),
                array(
                    'code'  => 'Flow03',
                    'title' => '見学・比較のサポート',
                    'body'  => "見学日程の調整を行い、見るべきポイントや比較ポイントを分かりやすく整理します。\n候補が絞れた後も、入居前に確認しておきたい点を整理しながら進めます。",
                    'image' => lh_theme_asset_uri('assets/media/support-flow-03.png'),
                ),
            ),
        ),
        'greeting' => array(
            'en_label'  => 'Greeting',
            'title'     => '代表挨拶',
            'name'      => '山田 太郎',
            'role'      => '代表',
            'body'      => array(
                '納得できる入居相談を、すべてのご家族へ。',
                '愛知で老人ホーム紹介を検討される方の多くは、急な退院や介護負担の増加など、時間の余裕がない中で判断を迫られます。',
                'だからこそ、費用や立地だけでなく、医療的な配慮、生活リズム、ご家族の通いやすさまで整理し、比べる順番を整えることが大切です。',
                'リアンハートでは、見学前の情報整理、確認項目の洗い出し、比較時の迷いの言語化まで伴走し、入居後のミスマッチを減らすご提案を心がけています。',
                '大切なご家族のこれからを、急がせず、曖昧にせず、一つずつ確認しながら進めてまいります。',
            ),
            'image'      => lh_theme_asset_uri('assets/media/greeting-cover.png'),
            'decoration' => 'Lian Heart',
        ),
        'knowledge' => array(
            'en_label' => 'Knowledge',
            'title'    => '入居前に知っておきたいこと',
            'items'    => array(
                array(
                    'title'      => $knowledge_articles[0]['card_title'],
                    'body'       => $knowledge_articles[0]['card_body'],
                    'url'        => $knowledge_articles[0]['url'],
                    'link_label' => '詳しく見る',
                ),
                array(
                    'title'      => $knowledge_articles[1]['card_title'],
                    'body'       => $knowledge_articles[1]['card_body'],
                    'url'        => $knowledge_articles[1]['url'],
                    'link_label' => '詳しく見る',
                ),
                array(
                    'title'      => $knowledge_articles[2]['card_title'],
                    'body'       => $knowledge_articles[2]['card_body'],
                    'url'        => $knowledge_articles[2]['url'],
                    'link_label' => '詳しく見る',
                ),
            ),
        ),
        'qa' => array(
            'en_label' => 'FAQ',
            'title'    => 'よくあるご質問',
            'items'    => array(
                array('question' => 'まだ何も決まっていない段階でも相談できますか？', 'answer' => 'はい。情報収集の段階からご相談いただけます。早めに条件を整理しておくことで、必要になったときに慌てず判断しやすくなります。'),
                array('question' => '愛知県のどこまで対応していますか？', 'answer' => '名古屋市をはじめ、尾張・知多・西三河・東三河など、愛知県全域でご相談を承ります。'),
                array('question' => '施設見学の日程調整はお願いできますか？', 'answer' => 'はい。候補施設の見学日程を調整し、比較しやすいよう確認ポイントも整理します。'),
                array('question' => '家族だけで相談しても大丈夫ですか？', 'answer' => 'はい。ご本人がすぐに動けない場合や、まずはご家族で情報整理したい場合もご相談いただけます。'),
                array('question' => '予算が限られていても相談できますか？', 'answer' => 'はい。ご予算の範囲で比較しやすい候補を整理し、費用面で確認したいポイントも分かりやすくご案内します。'),
                array('question' => '見学には同行してもらえますか？', 'answer' => '日程調整だけでなく、見学時に確認したい項目の整理や比較の視点づくりまでサポートします。'),
                array('question' => '夫婦で入居できる施設も紹介できますか？', 'answer' => 'はい。夫婦入居が可能な居室や受入条件を確認しながら、ご状況に合う候補をご案内します。'),
                array('question' => '家族だけで相談を進めることはできますか？', 'answer' => 'はい。まずはご家族だけで条件を整理し、その後にご本人を含めて比較を進める形でもご相談いただけます。'),
            ),
        ),
        'facility' => array(
            'en_label' => 'Facility',
            'title'    => 'ご紹介可能な施設の種類',
            'lead'     => '老人ホーム紹介・介護施設紹介では、ご本人の状態やご希望に応じて、以下のような施設種別から候補をご案内します。',
            'items'    => array(
                array('title' => '介護付有料老人ホーム', 'description' => '日常的な介護を受けながら生活したい方に向けた候補です。', 'image' => lh_theme_asset_uri('assets/media/facility-type-01-v2.jpg'), 'url' => '#contact'),
                array('title' => '住宅型有料老人ホーム', 'description' => '生活支援を受けながら、必要に応じて外部サービスの利用を検討したい方に向けた候補です。', 'image' => lh_theme_asset_uri('assets/media/facility-type-02-v2.jpg'), 'url' => '#contact'),
                array('title' => 'サービス付き高齢者向け住宅', 'description' => '見守りや生活相談を受けながら、自分らしい暮らしを続けたい方に向けた候補です。', 'image' => lh_theme_asset_uri('assets/media/facility-type-03-v2.jpg'), 'url' => '#contact'),
                array('title' => 'グループホーム', 'description' => '少人数の環境で落ち着いて生活したい方に向けた候補です。', 'image' => lh_theme_asset_uri('assets/media/facility-type-04.jpg'), 'url' => '#contact'),
            ),
        ),
        'company' => array(
            'en_label' => 'Company',
            'title'    => '運営会社',
            'visual'   => null,
            'rows'     => array(
                array('label' => '会社名', 'value' => 'リアンハート'),
                array('label' => '代表者', 'value' => '山田 太郎（サンプル）'),
                array('label' => '所在地', 'value' => '〒460-0000 愛知県名古屋市中区○○1-2-3 ○○ビル5F'),
                array('label' => '電話番号', 'value' => '052-000-0000'),
                array('label' => 'FAX', 'value' => '052-000-0001'),
                array('label' => 'メール', 'value' => 'info@example.co.jp'),
                array('label' => '営業時間', 'value' => '9:00-18:00'),
                array('label' => '定休日', 'value' => '土日祝'),
                array('label' => '事業内容', 'value' => '介護施設紹介 / 老人ホーム紹介 / 入居相談'),
            ),
        ),
        'contact' => array(
            'en_label'      => 'Contact',
            'title'         => '入居相談・お問い合わせ',
            'catch'         => '相談・見学調整・ご紹介はすべて無料です。',
            'lead_title'    => 'お問い合わせはフォームよりお願いいたします。',
            'lead_body'     => array(
                'ご相談内容を確認のうえ、2〜3営業日内を目安にご返信いたします。',
                'お急ぎの場合は、お電話でのご相談も承っています。',
            ),
            'notes'         => array(
                'ご入力いただいた情報は、お問い合わせへの回答やご連絡以外には使用いたしません。',
                'お問い合わせ内容によっては、回答できない場合がございますので、あらかじめご了承ください。',
                'お急ぎの場合は、お手数ですがお電話にてお問い合わせください。',
                '個人情報の取扱いについては、プライバシーポリシーをご確認ください。',
            ),
            'form_title'    => '入居相談フォーム',
            'success_title' => '送信ありがとうございました',
            'success_body'  => '内容を確認のうえ、担当よりご連絡いたします。',
            'recipient_email' => '',
        ),
    );
}

function lh_get_option_group($name) {
    if (function_exists('get_field') && function_exists('acf_add_options_page')) {
        $value = get_field('lh_' . $name, 'option');
        return is_array($value) ? $value : array();
    }

    $fallback = lh_get_fallback_options();
    $value = $fallback[$name] ?? array();
    return is_array($value) ? $value : array();
}

function lh_theme_data() {
    static $data = null;

    if ($data !== null) {
        return $data;
    }

    $defaults = lh_theme_defaults();
    $data = array();

    foreach ($defaults as $section => $section_defaults) {
        $data[$section] = lh_deep_merge($section_defaults, lh_get_option_group($section));
    }

    $data['hero']['slides'] = lh_merge_indexed_items($defaults['hero']['slides'], $data['hero']['slides'] ?? array());
    $data['hero']['ctas'] = lh_merge_indexed_items($defaults['hero']['ctas'], $data['hero']['ctas'] ?? array());
    $data['trust']['items'] = lh_merge_indexed_items($defaults['trust']['items'], $data['trust']['items'] ?? array());
    $data['timing']['items'] = lh_merge_indexed_items($defaults['timing']['items'], $data['timing']['items'] ?? array());
    $data['concept']['visuals'] = lh_merge_indexed_items($defaults['concept']['visuals'], $data['concept']['visuals'] ?? array());
    $data['pride']['cards'] = lh_merge_indexed_items($defaults['pride']['cards'], $data['pride']['cards'] ?? array());
    $data['menu']['cards'] = lh_merge_indexed_items($defaults['menu']['cards'], $data['menu']['cards'] ?? array());
    $data['knowledge']['items'] = lh_merge_indexed_items($defaults['knowledge']['items'], $data['knowledge']['items'] ?? array());
    $data['facility']['items'] = lh_merge_indexed_items($defaults['facility']['items'], $data['facility']['items'] ?? array());

    $data['hero']['ctas'] = array_values(array_slice(array_filter($data['hero']['ctas'], function ($item) {
        return is_array($item) && !empty($item['label']);
    }), 0, 2));

    if (empty($data['brand']['header_cta']) || !is_array($data['brand']['header_cta'])) {
        $data['brand']['header_cta'] = $defaults['brand']['header_cta'];
    }

    if (empty($data['timing']['cta']) || !is_array($data['timing']['cta'])) {
        $data['timing']['cta'] = $defaults['timing']['cta'];
    }

    foreach ($data['trust']['items'] as $index => $item) {
        $default = $defaults['trust']['items'][$index] ?? array();
        $data['trust']['items'][$index]['label'] = lh_fill_empty($item['label'] ?? null, $default['label'] ?? '');
        $data['trust']['items'][$index]['text'] = lh_fill_empty($item['text'] ?? null, $default['text'] ?? '');
    }

    foreach ($data['timing']['items'] as $index => $item) {
        $default = $defaults['timing']['items'][$index] ?? array();
        $data['timing']['items'][$index]['title'] = lh_fill_empty($item['title'] ?? null, $default['title'] ?? '');
        $data['timing']['items'][$index]['body'] = lh_fill_empty($item['body'] ?? null, $default['body'] ?? '');
    }

    foreach ($data['hero']['slides'] as $index => $slide) {
        $default = $defaults['hero']['slides'][$index] ?? array();
        $data['hero']['slides'][$index]['desktop_image'] = lh_fill_empty($slide['desktop_image'] ?? null, $default['desktop_image'] ?? null);
        $data['hero']['slides'][$index]['mobile_image'] = lh_fill_empty($slide['mobile_image'] ?? null, $default['mobile_image'] ?? null);
        $data['hero']['slides'][$index]['alt'] = lh_fill_empty($slide['alt'] ?? null, $default['alt'] ?? 'ヒーロー画像');
    }

    foreach ($data['concept']['visuals'] as $index => $visual) {
        $default = $defaults['concept']['visuals'][$index] ?? array();
        $data['concept']['visuals'][$index]['image'] = lh_fill_empty($visual['image'] ?? null, $default['image'] ?? null);
        $data['concept']['visuals'][$index]['alt'] = lh_fill_empty($visual['alt'] ?? null, $default['alt'] ?? 'コンセプト画像');
    }

    foreach ($data['pride']['cards'] as $index => $card) {
        $default = $defaults['pride']['cards'][$index] ?? array();
        $data['pride']['cards'][$index]['image'] = lh_fill_empty($card['image'] ?? null, $default['image'] ?? null);
        $data['pride']['cards'][$index]['code'] = lh_fill_empty($card['code'] ?? null, $default['code'] ?? '');
        $data['pride']['cards'][$index]['title'] = lh_fill_empty($card['title'] ?? null, $default['title'] ?? '');
        $data['pride']['cards'][$index]['body'] = lh_fill_empty($card['body'] ?? null, $default['body'] ?? '');
    }

    foreach ($data['menu']['cards'] as $index => $card) {
        $default = $defaults['menu']['cards'][$index] ?? array();
        $data['menu']['cards'][$index]['image'] = lh_fill_empty($card['image'] ?? null, $default['image'] ?? null);
        $data['menu']['cards'][$index]['code'] = lh_fill_empty($card['code'] ?? null, $default['code'] ?? '');
        $data['menu']['cards'][$index]['title'] = lh_fill_empty($card['title'] ?? null, $default['title'] ?? '');
        $data['menu']['cards'][$index]['body'] = lh_fill_empty($card['body'] ?? null, $default['body'] ?? '');
    }

    foreach ($data['knowledge']['items'] as $index => $item) {
        $default = $defaults['knowledge']['items'][$index] ?? array();
        $data['knowledge']['items'][$index]['title'] = lh_fill_empty($item['title'] ?? null, $default['title'] ?? '');
        $data['knowledge']['items'][$index]['body'] = lh_fill_empty($item['body'] ?? null, $default['body'] ?? '');
        $data['knowledge']['items'][$index]['url'] = lh_fill_empty($item['url'] ?? null, $default['url'] ?? '#contact');
        $data['knowledge']['items'][$index]['link_label'] = lh_fill_empty($item['link_label'] ?? null, $default['link_label'] ?? '詳しく見る');
    }

    foreach ($data['facility']['items'] as $index => $item) {
        $default = $defaults['facility']['items'][$index] ?? array();
        $data['facility']['items'][$index]['image'] = lh_fill_empty($item['image'] ?? null, $default['image'] ?? null);
        $data['facility']['items'][$index]['title'] = lh_fill_empty($item['title'] ?? null, $default['title'] ?? '');
        $data['facility']['items'][$index]['description'] = lh_fill_empty($item['description'] ?? null, $default['description'] ?? '');
        $data['facility']['items'][$index]['url'] = lh_fill_empty($item['url'] ?? null, $default['url'] ?? '#contact');
    }

    $concept_title = (string) ($data['concept']['title'] ?? '');
    if (strpos($concept_title, 'ご本人にもご家族にも') !== false || strpos($concept_title, '納得できる老人ホーム紹介を。') !== false) {
        $data['concept']['title'] = $defaults['concept']['title'];
    }

    $concept_body = array_values(array_filter(array_map('trim', lh_paragraphs($data['concept']['body'] ?? array()))));
    if (count($concept_body) < count($defaults['concept']['body'])) {
        $data['concept']['body'] = $defaults['concept']['body'];
    }

    $greeting_body = array_values(array_filter(array_map('trim', lh_paragraphs($data['greeting']['body'] ?? array()))));
    if (count($greeting_body) < count($defaults['greeting']['body'])) {
        $data['greeting']['body'] = $defaults['greeting']['body'];
    }

    return $data;
}

function lh_render_headline($english, $japanese, $modifiers = array()) {
    $classes = array('wp-headline', 'js-headline-fx');

    if (is_string($modifiers) && $modifiers !== '') {
        $modifiers = preg_split('/\s+/', trim($modifiers));
    }

    foreach ((array) $modifiers as $modifier) {
        if (!is_string($modifier) || trim($modifier) === '') {
            continue;
        }

        $classes[] = 'wp-headline--' . sanitize_html_class($modifier);
    }

    ob_start();
    ?>
    <header class="<?php echo esc_attr(implode(' ', array_unique($classes))); ?>">
        <p class="wp-headline__alphabetic"><?php echo esc_html(strtoupper((string) $english)); ?></p>
        <h2 class="wp-block-heading wp-headline__kana"><?php echo esc_html($japanese); ?></h2>
    </header>
    <?php
    return trim(ob_get_clean());
}

function lh_register_theme_supports() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_image_size('lh-hero-desktop', 1920, 1080, true);
    add_image_size('lh-hero-mobile', 900, 1400, true);
    add_image_size('lh-feature', 1400, 1000, true);
    add_image_size('lh-square', 960, 960, true);
}
add_action('after_setup_theme', 'lh_register_theme_supports');

function lh_enqueue_assets() {
    $theme_version = lh_theme_version();
    $style_file = get_stylesheet_directory() . '/style.css';
    $base_reset_file = get_template_directory() . '/assets/css/base-reset.css';
    $front_css_file = get_template_directory() . '/assets/css/front-page.css';
    $form_css_file = get_template_directory() . '/assets/css/form.css';
    $front_js_file = get_template_directory() . '/assets/js/front-page.js';
    $form_js_file = get_template_directory() . '/assets/js/form.js';
    $knowledge_article_css_file = get_template_directory() . '/assets/css/knowledge-article.css';
    $knowledge_article_js_file = get_template_directory() . '/assets/js/knowledge-article.js';

    $style_ver = file_exists($style_file) ? (string) filemtime($style_file) : $theme_version;
    $base_reset_ver = file_exists($base_reset_file) ? (string) filemtime($base_reset_file) : $theme_version;
    $front_css_ver = file_exists($front_css_file) ? (string) filemtime($front_css_file) : $theme_version;
    $form_css_ver = file_exists($form_css_file) ? (string) filemtime($form_css_file) : $theme_version;
    $front_js_ver = file_exists($front_js_file) ? (string) filemtime($front_js_file) : $theme_version;
    $form_js_ver = file_exists($form_js_file) ? (string) filemtime($form_js_file) : $theme_version;
    $knowledge_article_css_ver = file_exists($knowledge_article_css_file) ? (string) filemtime($knowledge_article_css_file) : $theme_version;
    $knowledge_article_js_ver = file_exists($knowledge_article_js_file) ? (string) filemtime($knowledge_article_js_file) : $theme_version;

    wp_enqueue_style('lh-theme-style', get_stylesheet_uri(), array(), $style_ver);
    wp_enqueue_style('lh-google-fonts', 'https://fonts.googleapis.com/css2?family=EB+Garamond:wght@500&family=Montserrat:wght@600;700&display=swap', array(), null);
    wp_enqueue_style('lh-swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.4.7');
    wp_enqueue_style('lh-base-reset', get_template_directory_uri() . '/assets/css/base-reset.css', array(), $base_reset_ver);
    wp_enqueue_style('lh-front-page', get_template_directory_uri() . '/assets/css/front-page.css', array('lh-base-reset', 'lh-google-fonts', 'lh-swiper'), $front_css_ver);
    wp_enqueue_style('lh-form', get_template_directory_uri() . '/assets/css/form.css', array('lh-front-page'), $form_css_ver);

    wp_enqueue_script('lh-swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.4.7', true);
    wp_enqueue_script('lh-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/gsap.min.js', array(), '3.12.7', true);
    wp_enqueue_script('lh-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollTrigger.min.js', array('lh-gsap'), '3.12.7', true);
    wp_enqueue_script('lh-splitting', get_template_directory_uri() . '/assets/vendor/splitting.min.js', array(), '1.0.6', true);
    wp_enqueue_script('lh-front-page', get_template_directory_uri() . '/assets/js/front-page.js', array('lh-swiper', 'lh-scrolltrigger', 'lh-splitting'), $front_js_ver, true);
    wp_enqueue_script('lh-form', get_template_directory_uri() . '/assets/js/form.js', array(), $form_js_ver, true);

    if (is_page_template('page-templates/template-knowledge-article.php')) {
        wp_enqueue_style('lh-knowledge-article', get_template_directory_uri() . '/assets/css/knowledge-article.css', array('lh-front-page'), $knowledge_article_css_ver);
        wp_enqueue_script('lh-knowledge-article', get_template_directory_uri() . '/assets/js/knowledge-article.js', array('lh-front-page'), $knowledge_article_js_ver, true);
    }

    wp_localize_script(
        'lh-form',
        'lhContact',
        array(
            'restUrl'  => esc_url_raw(rest_url('lian-heart/v1/contact')),
            'nonce'    => wp_create_nonce('wp_rest'),
            'messages' => array(
                'sending' => '送信しています。',
                'success' => '送信ありがとうございました。内容を確認のうえご連絡いたします。',
                'error'   => '送信に失敗しました。時間をおいて再度お試しください。',
                'required' => '必須項目を入力してください。',
                'confirm'  => '確認する',
                'back'     => '入力に戻る',
            ),
        )
    );
}
add_action('wp_enqueue_scripts', 'lh_enqueue_assets');

function lh_add_robots_meta() {
    if (wp_get_environment_type() !== 'production') {
        echo '<meta name="robots" content="noindex,nofollow">' . "\n";
    }
}
add_action('wp_head', 'lh_add_robots_meta', 1);

function lh_register_options_page() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page(array(
        'page_title' => 'Lian Heart Theme',
        'menu_title' => 'Lian Heart Theme',
        'menu_slug'  => 'lian-heart-theme-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ));
}
add_action('acf/init', 'lh_register_options_page');

