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

function lh_get_cta_url($item) {
    if (!is_array($item)) {
        return '#contact';
    }

    if (!empty($item['url'])) {
        return $item['url'];
    }

    return '#contact';
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
            'description' => "介護度、医療的な配慮、費用、立地、ご家族の通いやすさを整理しながら候補をご提案します。\n見学・比較検討まで丁寧に伴走し、入居後の『思っていたのと違った』を減らすための事前確認を大切にしています。",
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
        'concept' => array(
            'en_label' => 'Concept',
            'title'    => 'ご本人にもご家族にも、納得できる老人ホーム紹介を。',
            'lead'     => '介護施設紹介は、空室や料金だけで決められるものではありません。',
            'body'     => array(
                '今の身体状況、必要な介護や医療的な配慮、これからの暮らし方、ご家族の通いやすさや予算。大切なのは、条件を一つずつ整理しながら、無理のない選択肢を見つけることです。',
                'リアンハートでは、愛知県内で老人ホーム紹介をご検討中の方へ、入居相談の段階から見学前の情報整理、比較検討まで伴走し、入居後の「思っていたのと違った」を減らすご提案を行います。',
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
                'はじめまして。リアンハート代表の山田太郎です。',
                '老人ホーム紹介や介護施設紹介は、空室や費用だけで決められるものではありません。ご本人の状態、ご家族の不安、これからの暮らし方まで整理しながら、一つずつ判断していくことが大切だと考えています。',
                '比較しやすい形で情報を整理し、納得できる選択につながるよう入居相談から伴走します。',
            ),
            'image'      => lh_theme_asset_uri('assets/media/greeting-cover.png'),
            'decoration' => 'Lian Heart',
        ),
        'qa' => array(
            'en_label' => 'FAQ',
            'title'    => 'よくあるご質問',
            'items'    => array(
                array('question' => 'まだ何も決まっていない段階でも相談できますか？', 'answer' => 'はい。情報収集の段階からご相談いただけます。早めに条件を整理しておくことで、必要になったときに慌てず判断しやすくなります。'),
                array('question' => '愛知県のどこまで対応していますか？', 'answer' => '名古屋市をはじめ、尾張・知多・西三河・東三河など、愛知県全域でご相談を承ります。'),
                array('question' => '施設見学の日程調整はお願いできますか？', 'answer' => 'はい。候補施設の見学日程を調整し、比較しやすいよう確認ポイントも整理します。'),
                array('question' => '家族だけで相談しても大丈夫ですか？', 'answer' => 'はい。ご本人がすぐに動けない場合や、まずはご家族で情報整理したい場合もご相談いただけます。'),
            ),
        ),
        'facility' => array(
            'en_label' => 'Facility',
            'title'    => 'ご紹介可能な施設の種類',
            'lead'     => '老人ホーム紹介・介護施設紹介では、ご本人の状態やご希望に応じて、以下のような施設種別から候補をご案内します。',
            'items'    => array(
                array('title' => '介護付有料老人ホーム', 'description' => '日常的な介護を受けながら生活したい方に向けた候補です。', 'image' => null, 'url' => '#contact'),
                array('title' => '住宅型有料老人ホーム', 'description' => '生活支援を受けながら、必要に応じて外部サービスの利用を検討したい方に向けた候補です。', 'image' => null, 'url' => '#contact'),
                array('title' => 'サービス付き高齢者向け住宅', 'description' => '見守りや生活相談を受けながら、自分らしい暮らしを続けたい方に向けた候補です。', 'image' => null, 'url' => '#contact'),
                array('title' => 'グループホーム', 'description' => '少人数の環境で落ち着いて生活したい方に向けた候補です。', 'image' => null, 'url' => '#contact'),
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
            'catch'         => '老人ホーム紹介や介護施設紹介に関する入居相談を承っています。ご本人、ご家族、関係者の方からのご連絡に対応します。',
            'lead_title'    => 'お急ぎの場合は、お電話での入居相談をおすすめします。',
            'lead_body'     => array(
                'ご本人、ご家族、関係者の方からのご相談に対応します。',
                '希望エリア、月額予算、介護度、現在の生活状況、入居希望時期などが分かる範囲であるとスムーズです。',
            ),
            'notes'         => array(
                '会社名以外の掲載情報はサンプルです。実際の情報に差し替えてください。',
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

    if (empty($data['hero']['slides'])) {
        $data['hero']['slides'] = $defaults['hero']['slides'];
    }

    if (empty($data['hero']['ctas'])) {
        $data['hero']['ctas'] = $defaults['hero']['ctas'];
    }

    $data['hero']['ctas'] = array_values(array_slice(array_filter($data['hero']['ctas'], function ($item) {
        return is_array($item) && !empty($item['label']);
    }), 0, 2));

    if (empty($data['brand']['header_cta']) || !is_array($data['brand']['header_cta'])) {
        $data['brand']['header_cta'] = $defaults['brand']['header_cta'];
    }

    return $data;
}

function lh_render_headline($english, $japanese) {
    ob_start();
    ?>
    <header class="wp-headline js-headline-fx">
        <p class="wp-headline__sub"><?php echo esc_html($english); ?></p>
        <h2 class="wp-headline__main"><?php echo esc_html($japanese); ?></h2>
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
    wp_enqueue_style('lh-theme-style', get_stylesheet_uri(), array(), lh_theme_version());
    wp_enqueue_style('lh-google-fonts', 'https://fonts.googleapis.com/css2?family=EB+Garamond:wght@500&family=Montserrat:wght@600;700&display=swap', array(), null);
    wp_enqueue_style('lh-swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.4.7');
    wp_enqueue_style('lh-front-page', get_template_directory_uri() . '/assets/css/front-page.css', array('lh-google-fonts', 'lh-swiper'), lh_theme_version());
    wp_enqueue_style('lh-form', get_template_directory_uri() . '/assets/css/form.css', array('lh-front-page'), lh_theme_version());

    wp_enqueue_script('lh-swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.4.7', true);
    wp_enqueue_script('lh-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/gsap.min.js', array(), '3.12.7', true);
    wp_enqueue_script('lh-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.7/ScrollTrigger.min.js', array('lh-gsap'), '3.12.7', true);
    wp_enqueue_script('lh-front-page', get_template_directory_uri() . '/assets/js/front-page.js', array('lh-swiper', 'lh-scrolltrigger'), lh_theme_version(), true);
    wp_enqueue_script('lh-form', get_template_directory_uri() . '/assets/js/form.js', array(), lh_theme_version(), true);

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

