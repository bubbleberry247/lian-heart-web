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
        'knowledge-discharge-flow' => array(
            'slug' => 'knowledge-discharge-flow',
            'url' => home_url('/knowledge-discharge-flow/'),
            'en_label' => 'Knowledge',
            'title' => '病院から施設へ移るには｜退院調整の流れと相談先（地域連携室・MSW・ケアマネ）',
            'hero_image' => lh_theme_asset_uri('assets/media/support-flow-02.png'),
            'card_title' => '病院から施設へ移るには｜退院調整の流れと相談先（地域連携室・MSW・ケアマネ）',
            'card_body' => '病院から施設へ移るまでの流れを解説。MSW・地域連携室・ケアマネジャーの役割と、家族が準備すべきことを整理します。',
            'lead' => '病院から施設への移行は、病院の医療ソーシャルワーカーと家族・ケアマネジャーが連携して進めるのが基本の流れです。',
            'cta_title' => '入院中の今から動けば、選択肢は広がります',
            'cta_body' => '退院予定日をお聞かせください。',
        ),
        'knowledge-discharge-options' => array(
            'slug' => 'knowledge-discharge-options',
            'url' => home_url('/knowledge-discharge-options/'),
            'en_label' => 'Knowledge',
            'title' => '退院後すぐに入れる可能性がある施設はどれ？｜老健・有料老人ホーム・サ高住のつなぎ方',
            'hero_image' => lh_theme_asset_uri('assets/media/facility-type-02-v2.jpg'),
            'card_title' => '退院後すぐに入れる可能性がある施設はどれ？｜老健・有料老人ホーム・サ高住のつなぎ方',
            'card_body' => '退院後の受け皿になりやすい老健・有料老人ホーム・サ高住の役割と「つなぎ方」を解説。特養待機中の組み立て方も紹介します。',
            'lead' => '退院直後の受け皿は老健・有料老人ホーム・サ高住が中心で、特養は待機を見込んで「つなぎ」と並行して申し込むのが現実的です。',
            'cta_title' => '「どの種別から当たるべきか」だけでも、状態とご予算を伺えば整理できます',
            'cta_body' => '退院期限から逆算して、今日からの動き方を一緒に整理しませんか（相談無料）。',
        ),
        'knowledge-cost-aichi' => array(
            'slug' => 'knowledge-cost-aichi',
            'url' => home_url('/knowledge-cost-aichi/'),
            'en_label' => 'Knowledge',
            'title' => '愛知県の老人ホーム費用｜月額10万・15万・20万円で何が違うか',
            'hero_image' => lh_theme_asset_uri('assets/media/service-feature-01.jpg'),
            'card_title' => '愛知県の老人ホーム費用｜月額10万・15万・20万円で何が違うか',
            'card_body' => '愛知県の老人ホーム費用の内訳と、月額10万・15万・20万円で選べる施設の違いを解説。自己負担と軽減制度もまとめました。',
            'lead' => '老人ホームの月額は「介護保険の自己負担＋居住費＋食費＋その他」で決まり、予算帯ごとに選べる施設種別が変わります。',
            'cta_title' => '「月◯万円まで」が決まっていれば、そこから逆算してご提案できます',
            'cta_body' => '年金・ご予算の範囲で現実的な候補を一緒に探します（相談無料）。',
        ),
        'knowledge-cost-pension' => array(
            'slug' => 'knowledge-cost-pension',
            'url' => home_url('/knowledge-cost-pension/'),
            'en_label' => 'Knowledge',
            'title' => '年金だけで老人ホームに入れますか？｜愛知で「年金内」に近づける考え方',
            'hero_image' => lh_theme_asset_uri('assets/media/service-feature-02.jpg'),
            'card_title' => '年金だけで老人ホームに入れますか？｜愛知で「年金内」に近づける考え方',
            'card_body' => '年金だけで老人ホームに入れるかを愛知県の年金平均額をもとに解説。特養＋軽減制度で「年金内」に近づける考え方を紹介します。',
            'lead' => '年金だけで入れるかは年金額と施設種別の組み合わせ次第です。特養と軽減制度を使うと年金内に近づくケースがあります。',
            'cta_title' => '年金振込通知書が手元にあれば、現実的な候補のご提案までは早いです',
            'cta_body' => '年金・ご予算の範囲で現実的な候補を一緒に探します（相談無料）。',
        ),
        'knowledge-cost-hikazei' => array(
            'slug' => 'knowledge-cost-hikazei',
            'url' => home_url('/knowledge-cost-hikazei/'),
            'en_label' => 'Knowledge',
            'title' => '住民税非課税世帯の介護施設費用｜負担限度額認定（補足給付）と高額介護サービス費',
            'hero_image' => lh_theme_asset_uri('assets/media/service-feature-03.jpg'),
            'card_title' => '住民税非課税世帯の介護施設費用｜負担限度額認定（補足給付）と高額介護サービス費',
            'card_body' => '住民税非課税世帯が使える費用軽減制度を解説。負担限度額認定（補足給付）と高額介護サービス費の仕組みと申請先をまとめました。',
            'lead' => '住民税非課税世帯は、負担限度額認定で特養等の食費・居住費が軽減され、高額介護サービス費で自己負担にも上限が付きます。',
            'cta_title' => '課税状況と年金額が分かれば、使える制度の当たりを付けてから施設探しに入れます',
            'cta_body' => '年金・ご予算の範囲で現実的な候補を一緒に探します（相談無料）。',
        ),
        'knowledge-cost-iryouhikoujo' => array(
            'slug' => 'knowledge-cost-iryouhikoujo',
            'url' => home_url('/knowledge-cost-iryouhikoujo/'),
            'en_label' => 'Knowledge',
            'title' => '老人ホームの費用は医療費控除になりますか？｜対象になる費用・ならない費用',
            'hero_image' => lh_theme_asset_uri('assets/media/service-aside.jpg'),
            'card_title' => '老人ホームの費用は医療費控除になりますか？｜対象になる費用・ならない費用',
            'card_body' => '老人ホームの費用が医療費控除の対象になるかを施設種別ごとに解説。特養は2分の1、老健は全額など国税庁の取り扱いをまとめました。',
            'lead' => '医療費控除の扱いは施設種別で異なり、特養は自己負担の2分の1、老健・介護医療院は全額が対象になり得ます。',
            'cta_title' => '控除を踏まえた「実質負担」で施設を比べたい方は、状況をお聞かせください',
            'cta_body' => '年金・ご予算の範囲で現実的な候補を一緒に探します（相談無料）。',
        ),
        'knowledge-tokuyo-application' => array(
            'slug' => 'knowledge-tokuyo-application',
            'url' => home_url('/knowledge-tokuyo-application/'),
            'en_label' => 'Knowledge',
            'title' => '特養の申込みから入居まで｜愛知県での流れと準備',
            'hero_image' => lh_theme_asset_uri('assets/media/facility-type-01-v2.jpg'),
            'card_title' => '特養の申込みから入居まで｜愛知県での流れと準備',
            'card_body' => '特養の申込みから入居までの流れを愛知県向けに解説。要介護3以上の原則、要介護1・2の特例入所、複数申込みの実務まで。',
            'lead' => '特養は原則要介護3以上が対象で、施設ごとに申し込み、必要性の高い方から入居が決まる仕組みです。複数申込みもできます。',
            'cta_title' => '特養を考え始めた段階でも、申込みの段取りからご一緒できます',
            'cta_body' => '特養待機中の過ごし方・代替の組み立てを一緒に考えます（相談無料）。',
        ),
        'knowledge-tokuyo-waiting' => array(
            'slug' => 'knowledge-tokuyo-waiting',
            'url' => home_url('/knowledge-tokuyo-waiting/'),
            'en_label' => 'Knowledge',
            'title' => '特養の待機期間はどれくらい？｜待機中に検討する現実的な代替先',
            'hero_image' => lh_theme_asset_uri('assets/media/facility-type-03-v2.jpg'),
            'card_title' => '特養の待機期間はどれくらい？｜待機中に検討する現実的な代替先',
            'card_body' => '特養の待機者数の最新データ（愛知県3,502人）と待機期間の考え方を解説。待機中に検討すべき代替先も紹介。',
            'lead' => '愛知県の特養待機者は3,502人で減少傾向ですが、期間は施設差が大きく、待機中の代替先を持つことが現実的な備えです。',
            'cta_title' => '「待つ間どうするか」から一緒に設計します',
            'cta_body' => '特養待機中の過ごし方・代替の組み立てを一緒に考えます（相談無料）。',
        ),
        'knowledge-tokuyo-discharge-risk' => array(
            'slug' => 'knowledge-tokuyo-discharge-risk',
            'url' => home_url('/knowledge-tokuyo-discharge-risk/'),
            'en_label' => 'Knowledge',
            'title' => '退院期限があるのに特養だけを待つリスク',
            'hero_image' => lh_theme_asset_uri('assets/media/facility-type-04.jpg'),
            'card_title' => '退院期限があるのに特養だけを待つリスク',
            'card_body' => '退院期限があるのに特養の入居だけを待つことのリスクと、老健・有料老人ホームなどでつなぐ二段構えの考え方を解説します。',
            'lead' => '特養の入居時期は事前に読めないため、退院期限がある場合は受け皿を先に確保し、特養は並行で申し込むのが安全です。',
            'cta_title' => '退院期限と特養希望、両方あきらめない段取りを作ります',
            'cta_body' => '特養待機中の過ごし方・代替の組み立てを一緒に考えます（相談無料）。',
        ),
        'knowledge-dementia-refusal' => array(
            'slug' => 'knowledge-dementia-refusal',
            'url' => home_url('/knowledge-dementia-refusal/'),
            'en_label' => 'Knowledge',
            'title' => '認知症の親が施設を嫌がるとき、どう進めればいいですか？',
            'hero_image' => lh_theme_asset_uri('assets/media/concept-visual-01.jpg'),
            'card_title' => '認知症の親が施設を嫌がるとき、どう進めればいいですか？',
            'card_body' => '認知症の親が施設入居を嫌がるときの進め方を解説。段階を踏む方法と、家族だけで抱え込まないための相談先を紹介します。',
            'lead' => '説得より段階です。見学や体験利用など小さなステップを踏み、本人の不安の中身に合わせて進めると動きやすくなります。',
            'cta_title' => '「嫌がっていて話が進まない」段階のご相談こそ歓迎です',
            'cta_body' => '嫌がる・迷う・罪悪感——気持ちの整理から一緒に始めます（相談無料）。',
        ),
        'knowledge-dementia-timing' => array(
            'slug' => 'knowledge-dementia-timing',
            'url' => home_url('/knowledge-dementia-timing/'),
            'en_label' => 'Knowledge',
            'title' => '認知症の親を施設に入れるタイミングはいつですか？｜在宅介護の限界サイン',
            'hero_image' => lh_theme_asset_uri('assets/media/concept-visual-02.jpg'),
            'card_title' => '認知症の親を施設に入れるタイミングはいつですか？｜在宅介護の限界サイン',
            'card_body' => '認知症の親を施設に入れるタイミングの考え方を解説。在宅介護の限界サイン、特例入所の制度、早めに動く理由をまとめました。',
            'lead' => '「限界が来てから」では遅く、安全・健康・介護者の生活のいずれかにサインが出た時点が検討開始のタイミングです。',
            'cta_title' => '「まだ早いかも」と思った今が、調べ始めるのにちょうどいいタイミングです',
            'cta_body' => '嫌がる・迷う・罪悪感——気持ちの整理から一緒に始めます（相談無料）。',
        ),
        'knowledge-dementia-guilt' => array(
            'slug' => 'knowledge-dementia-guilt',
            'url' => home_url('/knowledge-dementia-guilt/'),
            'en_label' => 'Knowledge',
            'title' => '親を施設に入れるのは親不孝ですか？｜罪悪感との向き合い方',
            'hero_image' => lh_theme_asset_uri('assets/media/concept-visual-03.jpg'),
            'card_title' => '親を施設に入れるのは親不孝ですか？｜罪悪感との向き合い方',
            'card_body' => '「親を施設に入れるのは親不孝では」という罪悪感との向き合い方を解説。施設入居は介護の放棄ではなく、関わり方を変える選択です。',
            'lead' => '施設入居は介護をやめることではなく、介護の役割を専門職と分担し、家族にしかできない関わりに戻る選択です。',
            'cta_title' => '気持ちの整理からご一緒します',
            'cta_body' => '「決めきれない」段階のご相談で大丈夫です。',
        ),
    );
}

function lh_referrer_page_definition() {
    return array(
        'slug' => 'medical-care-professionals',
        'title' => '医療・介護関係者の方へ',
        'en_label' => 'Support',
        'lead' => '当社の主業務は老人介護施設の紹介です。ご本人・ご家族の意向を尊重しながら、施設選びの整理と比較支援を行っています。',
        'breadcrumb_home_label' => 'ホーム',
        'summary' => 'このページは、ご紹介をご検討いただく際に当社の進め方を共有するためのご案内です。',
        'policy_title' => 'ご紹介前にお伝えしたい方針',
        'principles' => array(
            array(
                'title' => '主業務について',
                'body' => '当社の主業務は、老人介護施設の紹介と施設選びのご相談対応です。',
            ),
            array(
                'title' => 'ご意向の整理と比較支援について',
                'body' => 'ご本人・ご家族の意向を丁寧に整理し、複数の候補を比較しやすい形に整えながらご案内します。',
            ),
            array(
                'title' => '必要な相談先のご案内について',
                'body' => '住まいの整理や各種手続きなど、周辺領域は自社で実行せず、必要に応じて地域の連携先をご案内します。',
            ),
            array(
                'title' => '入居後の見直しについて',
                'body' => '入居後に環境の見直しが必要になった場合や、別の施設への転居を希望される場合も、改めて施設選びから再相談をお受けします。',
            ),
            array(
                'title' => '情報共有について',
                'body' => 'ご本人やご家族の同意なく、外部へ情報を共有することはありません。',
            ),
        ),
        'flow_title' => '相談からご紹介までの流れ',
        'flow' => array(
            array(
                'title' => '1. ご状況の確認',
                'body' => '現在の生活状況や退院予定、介護負担、遠方家族のご事情などを確認します。',
            ),
            array(
                'title' => '2. ご意向の整理',
                'body' => 'ご本人・ご家族の希望や優先順位を整理し、施設選びの比較軸を明確にします。',
            ),
            array(
                'title' => '3. 候補比較と必要な相談先の整理',
                'body' => '候補施設の比較を進めながら、必要に応じて地域の連携先をご案内します。',
            ),
            array(
                'title' => '4. ご紹介と再相談',
                'body' => '見学やご紹介の後も、入居後の見直しや転居相談が必要な場合は改めて施設選びからご相談いただけます。',
            ),
        ),
        'cta' => array(
            'label' => 'トップページの相談窓口を見る',
            'url' => home_url('/#contact'),
            'style' => 'primary',
        ),
        'cta_text' => 'ご本人・ご家族のご意向を前提に、施設選びの整理からご相談をお受けしています。',
    );
}

function lh_referrer_page_slug() {
    $definition = lh_referrer_page_definition();
    return trim((string) ($definition['slug'] ?? ''), '/');
}

function lh_referrer_page_default_url() {
    $slug = lh_referrer_page_slug();
    return $slug !== '' ? home_url('/' . $slug . '/') : home_url('/');
}

function lh_get_referrer_page_url() {
    $slug = lh_referrer_page_slug();
    if ($slug === '') {
        return home_url('/');
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page instanceof WP_Post) {
        $permalink = get_permalink($page);
        if (is_string($permalink) && $permalink !== '') {
            return $permalink;
        }
    }

    return lh_referrer_page_default_url();
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
            'footer_note' => '愛知県での老人ホーム紹介・入居相談を、ご本人とご家族の状況整理からサポートしています。',
            'copyright'   => '© リアンハート All Rights Reserved.',
        ),
        'hero' => array(
            'eyebrow'     => '愛知県全域対応の老人ホーム紹介・入居相談',
            'title'       => "愛知で老人ホーム紹介を\nご希望の方へ。\n入居相談から比較まで伴走します。",
            'description' => "介護度、医療的な配慮、費用、立地、ご家族の通いやすさを整理しながら候補をご提案します。\nご家族が遠方にお住まいの場合も、何度も現地に足を運びにくい状況から比較しやすい形を一緒に整えます。",
            'slides'      => array(
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-01-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-01-mobile.jpg'),
                    'alt'           => '老人ホーム紹介の相談をするご家族とスタッフ',
                ),
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-02-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-02-mobile.jpg'),
                    'alt'           => '介護施設の見学前に条件を整理する相談風景',
                ),
                array(
                    'desktop_image' => lh_theme_asset_uri('assets/media/hero-slide-03-desktop.jpg'),
                    'mobile_image'  => lh_theme_asset_uri('assets/media/hero-slide-03-mobile.jpg'),
                    'alt'           => '愛知県で老人ホーム候補を比較する入居相談',
                ),
            ),
            'ctas' => array(
                array('label' => '無料相談フォームへ', 'url' => '#contact', 'style' => 'primary'),
                array('label' => '手数料と紹介範囲を見る', 'url' => '/fees-disclosure/', 'style' => 'ghost'),
            ),
        ),
        'trust' => array(
            'items' => array(
                array('label' => 'Free', 'text' => '相談・ご紹介はすべて無料'),
                array('label' => 'Area', 'text' => '愛知県全域でご相談対応'),
                array('label' => 'Compare', 'text' => '比較しやすい形に整理してご案内'),
                array('label' => 'Remote', 'text' => '遠方のご家族からのご相談にも対応'),
            ),
        ),
        'timing' => array(
            'en_label' => 'Timing',
            'title'    => 'こんなときにご相談ください',
            'items'    => array(
                array(
                    'title' => '退院後の生活が不安',
                    'body'  => '医療面や暮らし方も含めて、無理のない施設選びを整理したい方へ。',
                ),
                array(
                    'title' => '介護負担が大きくなってきた',
                    'body'  => 'ご家族だけで抱え込まず、比較の軸から相談したい方へ。',
                ),
                array(
                    'title' => 'ご家族が遠方に住んでいる',
                    'body'  => '何度も現地に足を運びにくい場合も、状況整理から進められます。',
                ),
                array(
                    'title' => '入居後に環境の見直しが必要になった',
                    'body'  => '別の施設への転居も含めて、改めて施設選びから相談したい方へ。',
                ),
            ),
            'cta' => array('label' => 'まずは状況を相談する', 'url' => '#contact', 'style' => 'primary'),
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
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-01.jpg'), 'alt' => '入居相談でご本人の希望と条件を整理する様子'),
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-02.jpg'), 'alt' => '老人ホーム紹介で施設候補を比較する資料'),
                array('image' => lh_theme_asset_uri('assets/media/concept-visual-03.jpg'), 'alt' => '介護施設選びを家族で話し合うイメージ'),
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
                    'body'  => "見学日程の調整を行い、見るべきポイントや比較ポイントを分かりやすく整理します。\n候補が絞れた後も、入居前に確認しておきたい点を整理しながら進めます。\n入居後に環境の見直しが必要になった場合や、別の施設への転居を希望される場合も、改めて施設選びからご相談いただけます。",
                    'image' => lh_theme_asset_uri('assets/media/support-flow-03.png'),
                ),
            ),
        ),
        'partner_support' => array(
            'headline_en' => 'SUPPORT',
            'headline_ja' => '必要な相談先も、状況に応じてご案内します',
            'intro'       => "まずは施設選びを中心に状況を整理し、そのうえで必要な相談先があればご案内します。\n住まいの整理や各種手続きなども、内容に応じて地域の連携先をご案内しています。\nご家族が遠方にお住まいの場合もご相談いただけます。必要に応じて相談先をご案内しながら、遠方からでも進めやすい形を一緒に整理します。",
            'cards'       => array(
                array(
                    'title' => '住まいの整理や退去について',
                    'body'  => "必要に応じてご案内できる先があります。\n内容に応じて、地域の連携先をご案内しています。",
                ),
                array(
                    'title' => '今後の住まいの活用について',
                    'body'  => "必要に応じてご案内できる先があります。\n内容に応じて、地域の連携先をご案内しています。",
                ),
                array(
                    'title' => '各種手続きやその後のご相談について',
                    'body'  => "必要に応じてご案内できる先があります。\n内容に応じて、地域の連携先をご案内しています。",
                ),
            ),
            'cta_label'           => 'まずは状況を相談する',
            'cta_url'             => '#contact',
            'referrer_link_label' => '医療・介護関係者の方へ',
            'referrer_link_url'   => lh_get_referrer_page_url(),
        ),
        'greeting' => array(
            'en_label'  => 'Greeting',
            'title'     => '代表挨拶',
            'name'      => '西田 江里',
            'role'      => '代表取締役',
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
                array('question' => '家族が遠方に住んでいても相談できますか？', 'answer' => 'はい。ご家族が遠方にお住まいの場合もご相談いただけます。何度も現地に足を運びにくい場合も、状況整理から進められます。'),
                array('question' => '家族だけで相談しても大丈夫ですか？', 'answer' => 'はい。ご本人がすぐに動けない場合や、まずはご家族で情報整理したい場合もご相談いただけます。'),
                array('question' => '予算が限られていても相談できますか？', 'answer' => 'はい。ご予算の範囲で比較しやすい候補を整理し、費用面で確認したいポイントも分かりやすくご案内します。'),
                array('question' => '見学には同行してもらえますか？', 'answer' => '日程調整だけでなく、見学時に確認したい項目の整理や比較の視点づくりまでサポートします。'),
                array('question' => '夫婦で入居できる施設も紹介できますか？', 'answer' => 'はい。夫婦入居が可能な居室や受入条件を確認しながら、ご状況に合う候補をご案内します。'),
                array('question' => '入居後の見直しや転居についても相談できますか？', 'answer' => 'はい。入居後に環境の見直しが必要になった場合や、別の施設への転居を希望される場合も、改めて施設選びからご相談いただけます。'),
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
                array('label' => '代表者', 'value' => '西田 江里'),
                array('label' => '所在地', 'value' => '〒450-0002 愛知県名古屋市中村区名駅4丁目24番5号 第2森ビル401'),
                array('label' => '電話番号', 'value' => ''),
                array('label' => 'FAX', 'value' => ''),
                array('label' => 'メール', 'value' => ''),
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
                '入居後に環境の見直しが必要になった場合や、別の施設への転居を希望される場合も、改めて施設選びからご相談いただけます。',
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
    $data['partner_support']['cards'] = lh_merge_indexed_items($defaults['partner_support']['cards'], $data['partner_support']['cards'] ?? array());
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

    foreach ($data['partner_support']['cards'] as $index => $card) {
        $default = $defaults['partner_support']['cards'][$index] ?? array();
        $data['partner_support']['cards'][$index]['title'] = lh_fill_empty($card['title'] ?? null, $default['title'] ?? '');
        $data['partner_support']['cards'][$index]['body'] = lh_fill_empty($card['body'] ?? null, $default['body'] ?? '');
    }

    $data['partner_support']['headline_en'] = lh_fill_empty($data['partner_support']['headline_en'] ?? null, $defaults['partner_support']['headline_en']);
    $data['partner_support']['headline_ja'] = lh_fill_empty($data['partner_support']['headline_ja'] ?? null, $defaults['partner_support']['headline_ja']);
    $data['partner_support']['intro'] = lh_fill_empty($data['partner_support']['intro'] ?? null, $defaults['partner_support']['intro']);
    $data['partner_support']['cta_label'] = lh_fill_empty($data['partner_support']['cta_label'] ?? null, $defaults['partner_support']['cta_label']);
    $data['partner_support']['cta_url'] = lh_fill_empty($data['partner_support']['cta_url'] ?? null, $defaults['partner_support']['cta_url']);
    $data['partner_support']['referrer_link_label'] = lh_fill_empty($data['partner_support']['referrer_link_label'] ?? null, $defaults['partner_support']['referrer_link_label']);
    $data['partner_support']['referrer_link_url'] = lh_fill_empty($data['partner_support']['referrer_link_url'] ?? null, lh_get_referrer_page_url());

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

    $reconsult_sentence = '入居後に環境の見直しが必要になった場合や、別の施設への転居を希望される場合も、改めて施設選びからご相談いただけます。';

    if (!empty($data['menu']['cards'][2]) && is_array($data['menu']['cards'][2])) {
        $flow3_paragraphs = lh_paragraphs($data['menu']['cards'][2]['body'] ?? '');
        if ($flow3_paragraphs === array()) {
            $flow3_paragraphs = lh_paragraphs($defaults['menu']['cards'][2]['body'] ?? '');
        }

        if (!in_array($reconsult_sentence, $flow3_paragraphs, true)) {
            $flow3_paragraphs[] = $reconsult_sentence;
        }

        $data['menu']['cards'][2]['body'] = implode("\n", array_values(array_unique($flow3_paragraphs)));
    }

    $qa_items = is_array($data['qa']['items'] ?? null) ? $data['qa']['items'] : array();
    $qa_json = wp_json_encode($qa_items, JSON_UNESCAPED_UNICODE);
    $has_reconsult_qa = false;
    foreach ($qa_items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $question = trim((string) ($item['question'] ?? ''));
        if ($question === '入居後の見直しや転居についても相談できますか？') {
            $has_reconsult_qa = true;
            break;
        }
    }

    if (
        !$has_reconsult_qa ||
        !is_string($qa_json) ||
        strpos($qa_json, '提携状況') !== false ||
        strpos($qa_json, 'すべての施設を紹介してもらえますか？') !== false ||
        strpos($qa_json, '施設見学の日程調整はお願いできますか？') !== false
    ) {
        $data['qa']['items'] = $defaults['qa']['items'];
    }

    $contact_catch = trim((string) ($data['contact']['catch'] ?? ''));
    if (
        $contact_catch === '' ||
        strpos($contact_catch, '関係者の方') !== false ||
        strpos($contact_catch, 'ご本人、ご家族、関係者') !== false
    ) {
        $data['contact']['catch'] = $defaults['contact']['catch'];
    }

    $contact_lead_body = array_values(array_filter(array_map('trim', lh_paragraphs($data['contact']['lead_body'] ?? array()))));
    $contact_lead_text = implode("\n", $contact_lead_body);
    if (
        $contact_lead_body === array() ||
        strpos($contact_lead_text, '関係者の方') !== false ||
        strpos($contact_lead_text, $reconsult_sentence) === false
    ) {
        $data['contact']['lead_body'] = $defaults['contact']['lead_body'];
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

function lh_ensure_referrer_page() {
    $definition = lh_referrer_page_definition();
    $slug = trim((string) ($definition['slug'] ?? ''), '/');
    $title = trim((string) ($definition['title'] ?? '医療・介護関係者の方へ'));
    $template = 'page-templates/template-referrer.php';

    if ($slug === '') {
        return;
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page instanceof WP_Post) {
        if (get_post_meta($page->ID, '_wp_page_template', true) !== $template) {
            update_post_meta($page->ID, '_wp_page_template', $template);
        }
        return;
    }

    $page_id = wp_insert_post(
        array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => '',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ),
        true
    );

    if (!is_wp_error($page_id) && (int) $page_id > 0) {
        update_post_meta((int) $page_id, '_wp_page_template', $template);
    }
}
add_action('after_switch_theme', 'lh_ensure_referrer_page');

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

function lh_plain_text($value, $max_length = 0) {
    if (is_array($value)) {
        $value = implode(' ', array_map('lh_plain_text', $value));
    }

    $text = wp_strip_all_tags((string) $value);
    $text = preg_replace('/\s+/u', ' ', trim($text));

    if ($max_length > 0 && function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $max_length) {
        return rtrim(mb_substr($text, 0, $max_length - 1, 'UTF-8')) . '…';
    }

    return $text;
}

function lh_document_title_parts($title) {
    $brand = lh_theme_data()['brand'] ?? array();
    $site_name = $brand['site_name'] ?? '';
    $tagline = $brand['tagline'] ?? '';

    if (empty($title['title'])) {
        $title['title'] = is_singular() ? single_post_title('', false) : $site_name;
    }
    if (empty($title['site']) && $site_name !== '') {
        $title['site'] = $site_name;
    }
    if (empty($title['tagline']) && $tagline !== '' && is_front_page()) {
        $title['tagline'] = $tagline;
    }
    return $title;
}
add_filter('document_title_parts', 'lh_document_title_parts');

function lh_override_site_icon($url) {
    if ($url !== '') {
        return $url;
    }
    $brand = lh_theme_data()['brand'] ?? array();
    return $brand['logo'] ?? '';
}
add_filter('get_site_icon_url', 'lh_override_site_icon');

function lh_output_favicon() {
    if (has_site_icon()) {
        return;
    }
    $brand = lh_theme_data()['brand'] ?? array();
    $logo_url = $brand['logo'] ?? '';
    if ($logo_url === '') {
        return;
    }
    echo '<link rel="icon" href="' . esc_url($logo_url) . '" type="image/png" sizes="any">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($logo_url) . '">' . "\n";
}
add_action('wp_head', 'lh_output_favicon', 1);

function lh_should_noindex() {
    if (defined('LH_FORCE_NOINDEX')) {
        return (bool) LH_FORCE_NOINDEX;
    }

    // WP管理画面「設定 > 表示設定 > 検索エンジンがインデックスしないようにする」が
    // ONのとき(blog_public=0)も noindex にする。公開切替を管理画面のチェックで行える。
    if (get_option('blog_public') === '0') {
        return true;
    }

    $host = wp_parse_url(home_url('/'), PHP_URL_HOST);
    $host = strtolower((string) ($host ?: ($_SERVER['HTTP_HOST'] ?? '')));

    return $host === '' ||
        $host === 'localhost' ||
        strpos($host, '127.') === 0 ||
        substr($host, -6) === '.local' ||
        substr($host, -5) === '.test';
}

function lh_get_company_row($label) {
    $company = lh_theme_data()['company'] ?? array();

    foreach ((array) ($company['rows'] ?? array()) as $row) {
        if (($row['label'] ?? '') === $label) {
            return lh_plain_text($row['value'] ?? '');
        }
    }

    return '';
}

function lh_is_placeholder_value($value) {
    if (is_array($value)) {
        $value = implode(' ', $value);
    }

    $text = trim((string) $value);
    if ($text === '') {
        return true;
    }

    return (bool) preg_match('/example|0000|○○|sample|サンプル|ダミー|052-000-000[01]/iu', $text);
}

function lh_get_meta_description() {
    if (is_singular()) {
        $article_meta_description = get_post_meta(get_the_ID(), 'lh_article_meta_description', true);
        if (is_string($article_meta_description) && trim($article_meta_description) !== '') {
            return lh_plain_text($article_meta_description, 155);
        }

        $excerpt = get_the_excerpt();
        if ($excerpt !== '') {
            return lh_plain_text($excerpt, 155);
        }
    }

    $theme = lh_theme_data();
    $hero = $theme['hero'] ?? array();
    $brand = $theme['brand'] ?? array();
    $description = $hero['description'] ?? ($brand['tagline'] ?? '');

    return lh_plain_text($description, 155);
}

function lh_get_canonical_url() {
    if (is_front_page()) {
        return home_url('/');
    }

    if (is_singular()) {
        $permalink = get_permalink();
        if (is_string($permalink) && $permalink !== '') {
            return $permalink;
        }
    }

    $request = isset($GLOBALS['wp']->request) ? trim((string) $GLOBALS['wp']->request, '/') : '';
    return $request !== '' ? home_url('/' . $request . '/') : home_url('/');
}

function lh_get_primary_image_url() {
    if (is_singular() && has_post_thumbnail()) {
        $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if (is_string($image) && $image !== '') {
            return $image;
        }
    }

    if (is_singular()) {
        $article_hero = function_exists('get_field') ? get_field('lh_article_hero_image') : null;
        if (is_array($article_hero) && !empty($article_hero['url'])) {
            return (string) $article_hero['url'];
        }

        $slug = (string) get_post_field('post_name', get_the_ID());
        $definition = lh_knowledge_article_definitions()[$slug] ?? array();
        if (!empty($definition['hero_image'])) {
            return (string) $definition['hero_image'];
        }
    }

    $theme = lh_theme_data();
    $hero = $theme['hero'] ?? array();
    $slides = (array) ($hero['slides'] ?? array());
    $first_slide = $slides[0] ?? array();

    return (string) ($first_slide['desktop_image'] ?? (($theme['brand']['logo'] ?? '')));
}

function lh_output_meta_tags() {
    if (is_admin()) {
        return;
    }

    $theme = lh_theme_data();
    $brand = $theme['brand'] ?? array();
    $title = wp_get_document_title();
    $description = lh_get_meta_description();
    $canonical = lh_get_canonical_url();
    $image = lh_get_primary_image_url();
    $site_name = $brand['site_name'] ?? get_bloginfo('name');

    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular() && !is_front_page() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";

    if ($image !== '') {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }

    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'lh_output_meta_tags', 2);

function lh_output_structured_data() {
    if (is_admin() || lh_should_noindex()) {
        return;
    }

    $theme = lh_theme_data();
    $brand = $theme['brand'] ?? array();
    $home_url = home_url('/');
    $canonical = lh_get_canonical_url();
    $site_name = lh_plain_text($brand['site_name'] ?? get_bloginfo('name'));
    $description = lh_get_meta_description();
    $logo = (string) ($brand['logo'] ?? '');
    $image = lh_get_primary_image_url();
    $phone = lh_get_company_row('電話番号');
    $email = lh_get_company_row('メール');
    $address = lh_get_company_row('所在地');
    $business_hours = lh_get_company_row('営業時間');

    $organization = array(
        '@type' => 'Organization',
        '@id' => $home_url . '#organization',
        'name' => $site_name,
        'url' => $home_url,
    );

    if ($logo !== '') {
        $organization['logo'] = $logo;
    }

    $local_business = array(
        '@type' => 'LocalBusiness',
        '@id' => $home_url . '#localbusiness',
        'name' => $site_name,
        'url' => $home_url,
        'description' => $description,
        'areaServed' => array(
            '@type' => 'AdministrativeArea',
            'name' => '愛知県全域',
        ),
        'priceRange' => '無料相談',
    );

    if ($image !== '') {
        $local_business['image'] = $image;
    }
    if (!lh_is_placeholder_value($phone)) {
        $local_business['telephone'] = $phone;
    }
    if (!lh_is_placeholder_value($email)) {
        $local_business['email'] = $email;
    }
    if (!lh_is_placeholder_value($address)) {
        $local_business['address'] = array(
            '@type' => 'PostalAddress',
            'streetAddress' => $address,
            'addressRegion' => '愛知県',
            'addressCountry' => 'JP',
        );
    }
    if (preg_match('/(\d{1,2}:\d{2})\s*[-〜]\s*(\d{1,2}:\d{2})/u', $business_hours, $matches)) {
        $local_business['openingHoursSpecification'] = array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                'opens' => $matches[1],
                'closes' => $matches[2],
            ),
        );
    }

    $graph = array(
        array(
            '@type' => 'WebSite',
            '@id' => $home_url . '#website',
            'url' => $home_url,
            'name' => $site_name,
            'inLanguage' => 'ja',
            'publisher' => array('@id' => $home_url . '#organization'),
        ),
        $organization,
        $local_business,
        array(
            '@type' => 'Service',
            '@id' => $home_url . '#service',
            'name' => '老人ホーム紹介・入居相談',
            'serviceType' => '介護施設紹介',
            'provider' => array('@id' => $home_url . '#localbusiness'),
            'areaServed' => array('@type' => 'AdministrativeArea', 'name' => '愛知県全域'),
            'description' => lh_plain_text($theme['hero']['description'] ?? $description),
        ),
        array(
            '@type' => 'WebPage',
            '@id' => $canonical . '#webpage',
            'url' => $canonical,
            'name' => wp_get_document_title(),
            'description' => $description,
            'inLanguage' => 'ja',
            'isPartOf' => array('@id' => $home_url . '#website'),
            'about' => array('@id' => $home_url . '#localbusiness'),
        ),
    );

    if (is_singular()) {
        $breadcrumb_items = array(
            array(
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'ホーム',
                'item' => $home_url,
            ),
        );

        if (is_page_template('page-templates/template-knowledge-article.php')) {
            $breadcrumb_items[] = array(
                '@type' => 'ListItem',
                'position' => 2,
                'name' => '入居前に知っておきたいこと',
                'item' => home_url('/#knowledge'),
            );
        }

        $breadcrumb_items[] = array(
            '@type' => 'ListItem',
            'position' => count($breadcrumb_items) + 1,
            'name' => lh_plain_text(get_the_title()),
            'item' => $canonical,
        );

        $graph[] = array(
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => $breadcrumb_items,
        );
    }

    if (is_singular() && is_page_template('page-templates/template-knowledge-article.php')) {
        $article = array(
            '@type' => 'Article',
            '@id' => $canonical . '#article',
            'headline' => lh_plain_text(get_the_title(), 110),
            'description' => $description,
            'inLanguage' => 'ja',
            'mainEntityOfPage' => array('@id' => $canonical . '#webpage'),
            'author' => array(
                '@type' => 'Organization',
                'name' => 'リアンハート編集部',
                'url' => home_url('/about-editorial-policy/'),
            ),
            'publisher' => array('@id' => $home_url . '#organization'),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
        );

        if ($image !== '') {
            $article['image'] = $image;
        }

        $graph[] = $article;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'lh_output_structured_data', 20);

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
                'consent'  => '同意が必要な項目にチェックを入れてください。',
            ),
        )
    );
}
add_action('wp_enqueue_scripts', 'lh_enqueue_assets');

function lh_add_robots_meta() {
    if (lh_should_noindex()) {
        echo '<meta name="robots" content="noindex,nofollow">' . "\n";
        return;
    }

    echo '<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">' . "\n";
}
add_action('wp_head', 'lh_add_robots_meta', 1);

function lh_filter_robots_txt($output, $public) {
    if (lh_should_noindex() || (int) $public !== 1) {
        return $output;
    }

    $ai_policy = array(
        '',
        '# AI search crawler policy',
        'User-agent: OAI-SearchBot',
        'Allow: /',
        'User-agent: ChatGPT-User',
        'Allow: /',
        'User-agent: Claude-SearchBot',
        'Allow: /',
        'User-agent: Claude-User',
        'Allow: /',
        'User-agent: PerplexityBot',
        'Allow: /',
        'User-agent: GPTBot',
        'Disallow: /',
        'User-agent: ClaudeBot',
        'Disallow: /',
        'User-agent: Google-Extended',
        'Disallow: /',
        'User-agent: CCBot',
        'Disallow: /',
        'Sitemap: ' . home_url('/wp-sitemap.xml'),
    );

    return rtrim($output) . "\n" . implode("\n", $ai_policy) . "\n";
}
add_filter('robots_txt', 'lh_filter_robots_txt', 20, 2);

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

/* ============================================================
 * Security hardening
 * この区切り以降を削除すれば従来挙動に戻せる（ロールバック単位）。
 * すべて管理画面(is_admin)は対象外。
 * ============================================================ */

// 1. WordPress バージョン情報の露出を抑止（generator meta / RSS generator）
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// 2-a. REST API の users エンドポイントを公開しない（メール/ログイン名の列挙防止）
function lh_restrict_rest_users($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
}
add_filter('rest_endpoints', 'lh_restrict_rest_users');

// 2-b. 著者アーカイブ(/author/...)を無効化してトップへ301
function lh_disable_author_archive() {
    if (is_author()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'lh_disable_author_archive');
add_filter('author_link', function () {
    return home_url('/');
});

// 2-c. ?author=N によるID→ログイン名の逆引きをトップへ301
function lh_block_author_query() {
    if (is_admin()) {
        return;
    }
    if (isset($_GET['author']) && preg_match('/^\d+$/', (string) $_GET['author'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('init', 'lh_block_author_query');

// 3. XML-RPC を無効化（Pingback ヘッダも除去）
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', function () {
    return array();
});
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// 4. セキュリティ HTTP ヘッダー（フロントのみ）
//    CSP は外部依存(Google Fonts/jsDelivr/cdnjs/GAS/Google Maps)があるため
//    ここには含めず、サーバー側(.htaccess)で Report-Only から段階導入する。
function lh_send_security_headers() {
    if (is_admin()) {
        return;
    }
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}
add_action('send_headers', 'lh_send_security_headers');

// 5. ログインエラーでユーザー名/パスワードの別を秘匿
add_filter('login_errors', function () {
    return 'ログイン情報が正しくありません。';
});

/* llms.txt — AI/LLM向けサイト要約 (2026). /llms.txt配信, home_url自動追従 */
function lh_render_llms_txt() {
    $home = untrailingslashit(home_url('/'));
    $theme = lh_theme_data();
    $brand = $theme['brand'] ?? array();
    $name = lh_plain_text($brand['site_name'] ?? 'リアンハート');
    $articles = lh_knowledge_article_definitions();
    $lines = array();
    $lines[] = '# ' . $name;
    $lines[] = '';
    $lines[] = '> 愛知県全域に対応する老人ホーム紹介・入居相談サービス。介護付有料老人ホーム・住宅型有料老人ホーム・サービス付き高齢者向け住宅・グループホームから、ご本人の状態とご家族の希望に合わせて候補を整理し、比較・見学調整まで無料で伴走します。';
    $lines[] = '';
    $lines[] = '費用や立地だけでなく、医療的な配慮・生活リズム・ご家族の通いやすさまで整理し、入居後のミスマッチを減らす入居相談を提供。遠方のご家族からのご相談にも対応しています。';
    $lines[] = '';
    $lines[] = '## 主要ページ';
    $lines[] = '- [トップページ](' . $home . '/): サービス概要・相談からご紹介までの流れ・よくあるご質問';
    $lines[] = '- [医療・介護関係者の方へ](' . lh_get_referrer_page_url() . '): ご紹介前の方針と相談からご紹介までの流れ';
    $lines[] = '';
    $lines[] = '## 知識記事';
    foreach ($articles as $a) {
        if (!is_array($a)) { continue; }
        $lines[] = '- [' . $a['title'] . '](' . $a['url'] . '): ' . $a['card_body'];
    }
    $lines[] = '';
    $lines[] = '## サービス内容';
    $lines[] = '- 対応地域: 愛知県全域（名古屋市・尾張・知多・西三河・東三河）';
    $lines[] = '- 紹介施設: 介護付有料老人ホーム / 住宅型有料老人ホーム / サービス付き高齢者向け住宅 / グループホーム';
    $lines[] = '- 料金: 相談・施設紹介・見学調整はすべて無料';
    $lines[] = '';
    return implode("\n", $lines) . "\n";
}
add_action('template_redirect', function () {
    $uri = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?');
    if (rtrim($uri, '/') !== '/llms.txt') { return; }
    nocache_headers();
    header('Content-Type: text/plain; charset=utf-8');
    status_header(200);
    echo lh_render_llms_txt();
    exit;
}, 0);
