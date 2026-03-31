<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$theme = lh_theme_data();
$brand = $theme['brand'];
$hero = $theme['hero'];
$trust = $theme['trust'];
$timing = $theme['timing'];
$concept = $theme['concept'];
$pride = $theme['pride'];
$menu = $theme['menu'];
$greeting = $theme['greeting'];
$knowledge = $theme['knowledge'];
$qa = $theme['qa'];
$facility = $theme['facility'];
$company = $theme['company'];
$contact = $theme['contact'];
$section_ctas = array_slice($hero['ctas'], 0, 2);
$concept_title = (string) ($concept['title'] ?? '');
if (strpos($concept_title, "\n") === false && strpos($concept_title, '納得できる老人ホーム紹介を。') !== false) {
    $concept_title = str_replace(
        '納得できる老人ホーム紹介を。',
        "納得できる\n老人ホーム紹介を。",
        $concept_title
    );
}
$concept_title_lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $concept_title))));
?>
<main class="site-main front-page">
    <section class="section mv" id="mv">
        <div class="mv__inner">
            <div class="mv-slider-holder">
                <div class="hero__slider swiper" id="js-hero-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($hero['slides'] as $index => $slide) : ?>
                            <?php
                            $desktop = lh_resolve_image($slide['desktop_image'] ?? null, sprintf('Hero %02d', $index + 1), 1920, 1080);
                            $mobile = lh_resolve_image($slide['mobile_image'] ?? null, sprintf('Hero SP %02d', $index + 1), 900, 1400);
                            ?>
                            <div class="swiper-slide hero-slide">
                                <picture class="hero-slide__media">
                                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($mobile['url']); ?>">
                                    <img src="<?php echo esc_url($desktop['url']); ?>" alt="<?php echo esc_attr($slide['alt'] ?? $desktop['alt']); ?>">
                                </picture>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="mv__overlay"></div>
            <div class="mv__content js-hero-copy-fx">
                <p class="mv__eyebrow"><?php echo esc_html($hero['eyebrow']); ?></p>
                <h1 class="mv__title"><?php echo nl2br(esc_html($hero['title'])); ?></h1>
                <div class="mv__description">
                    <?php foreach (lh_paragraphs($hero['description']) as $line) : ?>
                        <p><?php echo esc_html($line); ?></p>
                    <?php endforeach; ?>
                </div>
                <div class="mv__actions">
                    <?php foreach (array_slice($hero['ctas'], 0, 2) as $cta) : ?>
                        <?php echo lh_render_button($cta, 'mv__action'); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="scrolldown-indicator" type="button" data-scroll-target="#concept" aria-label="下へスクロール">
                <span class="scrolldown-indicator__ring">SCROLL DOWN SCROLL DOWN</span>
                <span class="scrolldown-indicator__arrow"></span>
            </button>
        </div>
    </section>

    <section class="trust" id="trust">
        <div class="constrained-content">
            <div class="trust-points js-trust-points">
                <?php foreach ((array) ($trust['items'] ?? array()) as $item) : ?>
                    <?php
                    $label = trim((string) ($item['label'] ?? ''));
                    $text = trim((string) ($item['text'] ?? ''));
                    if ($label === '' && $text === '') {
                        continue;
                    }
                    ?>
                    <article class="trust-point">
                        <?php if ($label !== '') : ?>
                            <p class="trust-point__label"><?php echo esc_html(strtoupper($label)); ?></p>
                        <?php endif; ?>
                        <?php if ($text !== '') : ?>
                            <p class="trust-point__text"><?php echo esc_html($text); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section timing" id="timing">
        <div class="constrained-content">
            <?php echo lh_render_headline($timing['en_label'] ?? 'Timing', $timing['title'] ?? 'こんなときにご相談ください', array('section', 'timing')); ?>
            <div class="timing-grid">
                <?php foreach ((array) ($timing['items'] ?? array()) as $item) : ?>
                    <?php
                    $title = trim((string) ($item['title'] ?? ''));
                    $body = trim((string) ($item['body'] ?? ''));
                    if ($title === '' && $body === '') {
                        continue;
                    }
                    ?>
                    <article class="timing-card js-timing-card-fx">
                        <?php if ($title !== '') : ?>
                            <h3 class="timing-card__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                        <?php if ($body !== '') : ?>
                            <p class="timing-card__body"><?php echo esc_html($body); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="timing__actions">
                <?php echo lh_render_button($timing['cta'] ?? array('label' => '相談してみる', 'url' => '#contact', 'style' => 'primary'), 'timing__action'); ?>
            </div>
        </div>
    </section>

    <section class="concept" id="concept">
        <div class="concept__body">
            <div class="concept-contents">
                <figure class="concept-visual concept-visual-1 js-concept-visual-fx">
                    <?php $visual = lh_resolve_image($concept['visuals'][0]['image'] ?? null, $concept['visuals'][0]['alt'] ?? 'Concept 01', 800, 1200); ?>
                    <img src="<?php echo esc_url($visual['url']); ?>" alt="<?php echo esc_attr($visual['alt']); ?>">
                </figure>
                <div class="concept-contents__inner">
                    <h2 class="wp-block-heading concept__title has-text-align-left">
                        <?php foreach ($concept_title_lines as $index => $line) : ?>
                            <?php $is_last_line = $index === count($concept_title_lines) - 1; ?>
                            <span class="concept__title-line<?php echo $is_last_line ? ' concept__title-line--nowrap' : ''; ?>"><?php echo esc_html($line); ?></span>
                        <?php endforeach; ?>
                    </h2>
                    <div class="concept__text">
                        <?php
                        foreach ($concept['body'] as $index => $paragraph) {
                            $text = is_array($paragraph) ? ($paragraph['text'] ?? '') : $paragraph;
                            if ($text === '') {
                                continue;
                            }

                            if ($index === 1) {
                                $text = str_replace('。だからこそ、', "。<br class=\"u-hide-sp\">だからこそ、", $text);
                            }

                            if ($index === 2) {
                                $text = str_replace('方へ、', "方へ、<br class=\"u-hide-sp\">", $text);
                                $text = str_replace('暮らし方まで', "暮らし方まで<br class=\"u-hide-sp\">", $text);
                            }

                            echo '<p>' . wp_kses($text, array('br' => array('class' => true))) . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <figure class="concept-visual concept-visual-2 js-concept-visual-fx">
                <?php $visual = lh_resolve_image($concept['visuals'][1]['image'] ?? null, $concept['visuals'][1]['alt'] ?? 'Concept 02', 800, 800); ?>
                <img src="<?php echo esc_url($visual['url']); ?>" alt="<?php echo esc_attr($visual['alt']); ?>">
            </figure>
            <figure class="concept-visual concept-visual-3 js-concept-visual-fx">
                <?php $visual = lh_resolve_image($concept['visuals'][2]['image'] ?? null, $concept['visuals'][2]['alt'] ?? 'Concept 03', 1200, 800); ?>
                <img src="<?php echo esc_url($visual['url']); ?>" alt="<?php echo esc_attr($visual['alt']); ?>">
            </figure>
        </div>
    </section>

    <section class="section pride" id="pride">
        <div class="constrained-content">
            <?php echo lh_render_headline($pride['en_label'], $pride['title'], array('section', 'pride')); ?>
            <div class="pride-points">
                <?php foreach ($pride['cards'] as $index => $card) : ?>
                    <?php $image = lh_resolve_image($card['image'] ?? null, $card['title'] ?? sprintf('Service %02d', $index + 1), 1400, 1000); ?>
                    <article class="pride-point js-pride-point-fx <?php echo $index % 2 === 1 ? 'is-reverse' : ''; ?>">
                        <div class="pride-point__body">
                            <p class="pride-point__code"><?php echo esc_html($card['code'] ?? sprintf('Service%02d', $index + 1)); ?></p>
                            <h3 class="pride-point__title"><?php echo esc_html($card['title'] ?? ''); ?></h3>
                            <div class="pride-point__text">
                                <?php foreach (lh_paragraphs($card['body'] ?? '') as $paragraph) : ?>
                                    <p><?php echo esc_html($paragraph); ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <figure class="pride-point__fig-block">
                            <span class="image-wipe" aria-hidden="true"></span>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                        </figure>
                    </article>
                <?php endforeach; ?>
            </div>
            <aside class="pride-aside" id="pride-aside">
                <?php $aside_image = lh_resolve_image($pride['side_image'] ?? null, $pride['side_title'] ?? 'Aside', 900, 720); ?>
                <figure class="pride-aside__image">
                    <span class="image-wipe" aria-hidden="true"></span>
                    <img src="<?php echo esc_url($aside_image['url']); ?>" alt="<?php echo esc_attr($aside_image['alt']); ?>">
                </figure>
                <div class="pride-aside__body">
                    <h3><?php echo esc_html($pride['side_title']); ?></h3>
                    <?php foreach (lh_paragraphs($pride['side_body']) as $paragraph) : ?>
                        <p><?php echo esc_html($paragraph); ?></p>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
        <div class="rail-horizontal-txt" aria-hidden="true">
            <div class="rail-horizontal-txt__body">
                <p><?php echo esc_html($pride['rail_text']); ?></p>
                <p><?php echo esc_html($pride['rail_text']); ?></p>
            </div>
        </div>
    </section>

    <section class="section menu" id="menu">
        <div class="constrained-content">
            <?php echo lh_render_headline($menu['en_label'], $menu['title'], array('section', 'menu')); ?>
            <div class="featured-menu-list">
                <?php foreach ($menu['cards'] as $index => $card) : ?>
                    <?php $image = lh_resolve_image($card['image'] ?? null, $card['title'] ?? sprintf('Flow %02d', $index + 1), 1400, 1000); ?>
                    <article class="menu-item js-menu-item-fx <?php echo $index % 2 === 1 ? 'is-reverse' : ''; ?>">
                        <div class="menu-item__body">
                            <p class="menu-item__code"><?php echo esc_html($card['code'] ?? sprintf('Flow%02d', $index + 1)); ?></p>
                            <h3 class="menu-item__title"><?php echo esc_html($card['title'] ?? ''); ?></h3>
                            <div class="menu-item__text">
                                <?php foreach (lh_paragraphs($card['body'] ?? '') as $paragraph) : ?>
                                    <p><?php echo esc_html($paragraph); ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <figure class="menu-item__fig">
                            <span class="image-wipe" aria-hidden="true"></span>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                        </figure>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="greeting wp-custom-section js-greeting" id="greeting">
        <?php
        $greeting_image = lh_resolve_image($greeting['image'] ?? null, $greeting['name'] ?? 'Greeting', 1200, 1400);
        $greeting_identity = array();
        $greeting_paragraphs = array();

        if (!empty($greeting['role'])) {
            $greeting_identity[] = $greeting['role'];
        }

        if (!empty($greeting['name'])) {
            $greeting_identity[] = $greeting['name'];
        }

        foreach ((array) ($greeting['body'] ?? array()) as $paragraph) {
            $text = is_array($paragraph) ? ($paragraph['text'] ?? '') : $paragraph;
            if ($text === '') {
                continue;
            }

            $greeting_paragraphs[] = $text;
        }
        ?>
        <?php echo lh_render_headline($greeting['en_label'], $greeting['title'], array('greeting')); ?>
        <div class="greeting__body">
            <figure class="greeting-cover">
                <span class="image-wipe" aria-hidden="true"></span>
                <img src="<?php echo esc_url($greeting_image['url']); ?>" alt="<?php echo esc_attr($greeting_image['alt']); ?>">
                <figcaption class="greeting-cover__caption">
                    <p class="greeting-cover__catch" aria-hidden="true"><?php echo esc_html(strtoupper($greeting['en_label'] ?? 'Greeting')); ?></p>
                </figcaption>
            </figure>
            <div class="greeting__guts">
                <?php if (!empty($greeting_identity)) : ?>
                    <p class="greeting__identity">
                        <?php foreach ($greeting_identity as $index => $line) : ?>
                            <?php if ($index > 0) : ?><br><?php endif; ?>
                            <?php echo esc_html($line); ?>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($greeting_paragraphs)) : ?>
                    <p class="greeting__separator" aria-hidden="true">-</p>
                    <?php foreach ($greeting_paragraphs as $index => $paragraph) : ?>
                        <p class="greeting__paragraph<?php echo $index === 0 ? ' greeting__lead' : ''; ?>"><?php echo esc_html($paragraph); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section knowledge" id="knowledge">
        <div class="constrained-content">
            <?php echo lh_render_headline($knowledge['en_label'] ?? 'Knowledge', $knowledge['title'] ?? '入居前に知っておきたいこと', array('section', 'knowledge')); ?>
            <div class="knowledge-grid">
                <?php foreach ((array) ($knowledge['items'] ?? array()) as $item) : ?>
                    <?php
                    $title = trim((string) ($item['title'] ?? ''));
                    $body = trim((string) ($item['body'] ?? ''));
                    $url = trim((string) ($item['url'] ?? '#contact'));
                    $link_label = trim((string) ($item['link_label'] ?? '詳しく見る'));
                    if ($title === '' && $body === '') {
                        continue;
                    }
                    ?>
                    <article class="knowledge-card js-knowledge-card-fx">
                        <?php if ($title !== '') : ?>
                            <h3 class="knowledge-card__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                        <?php if ($body !== '') : ?>
                            <p class="knowledge-card__body"><?php echo esc_html($body); ?></p>
                        <?php endif; ?>
                        <a class="knowledge-card__link" href="<?php echo esc_url($url !== '' ? $url : '#contact'); ?>"><?php echo esc_html($link_label !== '' ? $link_label : '詳しく見る'); ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section qa" id="qa">
        <div class="constrained-content qa__columns">
            <div class="qa__heading-block">
                <?php echo lh_render_headline($qa['en_label'], $qa['title'], array('section', 'qa')); ?>
            </div>
            <div class="qa-list">
                <?php foreach (array_slice((array) $qa['items'], 0, 8) as $index => $item) : ?>
                    <?php $question = $item['question'] ?? ''; ?>
                    <?php $answer = $item['answer'] ?? ''; ?>
                    <?php if ($question === '' || $answer === '') { continue; } ?>
                    <details class="qa-item" <?php echo $index === 0 ? 'open' : ''; ?>>
                        <summary>
                            <span class="qa-item__label">Q</span>
                            <span><?php echo esc_html($question); ?></span>
                        </summary>
                        <div class="qa-item__answer">
                            <?php foreach (lh_paragraphs($answer) as $paragraph) : ?>
                                <p><?php echo esc_html($paragraph); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section facility" id="facility">
        <div class="constrained-content">
            <?php echo lh_render_headline($facility['en_label'], $facility['title'], array('section', 'facility')); ?>
            <p class="section-lead"><?php echo esc_html($facility['lead']); ?></p>
            <div class="facility-grid">
                <?php foreach (array_slice((array) $facility['items'], 0, 3) as $item) : ?>
                    <?php $image = lh_resolve_image($item['image'] ?? null, $item['title'] ?? 'Facility', 900, 760); ?>
                    <article class="facility-card">
                        <figure class="facility-card__image">
                            <span class="image-wipe" aria-hidden="true"></span>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                        </figure>
                        <div class="facility-card__body">
                            <h3><?php echo esc_html($item['title'] ?? ''); ?></h3>
                            <p><?php echo esc_html($item['description'] ?? ''); ?></p>
                            <a class="facility-card__link" href="<?php echo esc_url($item['url'] ?? '#contact'); ?>">詳しく見る</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section shop-info" id="shop-info">
        <div class="constrained-content">
            <?php echo lh_render_headline($company['en_label'], $company['title'], array('section', 'shop-info')); ?>
            <div class="shop-info__body">
                <?php $maps_url = $company['maps_embed_url'] ?? ''; ?>
                <?php if ($maps_url !== '') : ?>
                <figure class="shop-visual shop-visual--map">
                    <iframe
                        src="<?php echo esc_url($maps_url); ?>"
                        title="リアンハート所在地の地図"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </figure>
                <?php endif; ?>
                <div class="shop-info__guts">
                    <table class="shop-info-table">
                        <tbody>
                            <?php foreach ($company['rows'] as $row) : ?>
                                <?php if (empty($row['label']) || empty($row['value'])) { continue; } ?>
                                <tr>
                                    <th><?php echo esc_html($row['label']); ?></th>
                                    <td><?php echo esc_html(is_array($row['value']) ? implode(' ', $row['value']) : $row['value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="section contact" id="contact">
        <div class="constrained-content">
            <?php echo lh_render_headline($contact['en_label'], $contact['title'], array('section', 'contact')); ?>
            <p class="contact-catch"><?php echo esc_html($contact['catch']); ?></p>
            <div class="contact__body">
                <div class="contact-lead-block">
                    <h3><?php echo esc_html($contact['lead_title']); ?></h3>
                    <div class="contact-lead-block__text">
                        <?php
                        foreach ($contact['lead_body'] as $paragraph) {
                            $text = is_array($paragraph) ? ($paragraph['text'] ?? '') : $paragraph;
                            if ($text === '') {
                                continue;
                            }
                            echo '<p>' . esc_html($text) . '</p>';
                        }
                        ?>
                    </div>
                    <?php if (!empty($section_ctas)) : ?>
                        <div class="section-actions">
                            <?php foreach ($section_ctas as $cta) : ?>
                                <?php echo lh_render_button($cta, 'section-actions__item'); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contact['notes'])) : ?>
                        <ul class="contact-note-list">
                            <?php foreach ($contact['notes'] as $note) : ?>
                                <?php $text = is_array($note) ? ($note['text'] ?? '') : $note; ?>
                                <?php if ($text === '') { continue; } ?>
                                <li><?php echo esc_html($text); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="contact-form-block">
                    <div class="contact-form-block__inner">
                        <h3 class="contact-form-block__title"><?php echo esc_html($contact['form_title']); ?></h3>
                        <div class="contact-form-success" hidden>
                            <h4><?php echo esc_html($contact['success_title']); ?></h4>
                            <p><?php echo esc_html($contact['success_body']); ?></p>
                        </div>
                        <form class="contact-form js-contact-form" novalidate>
                            <div class="contact-form__grid">
                                <label class="contact-field">
                                    <span>お名前<span class="required">*</span></span>
                                    <input type="text" name="name" placeholder="お名前をご入力ください。" required>
                                </label>
                                <label class="contact-field">
                                    <span>メールアドレス<span class="required">*</span></span>
                                    <input type="email" name="email" placeholder="info@example.co.jp" required>
                                </label>
                                <label class="contact-field">
                                    <span>電話番号<span class="required">*</span></span>
                                    <input type="tel" name="phone" inputmode="tel" required>
                                </label>
                                <label class="contact-field contact-field--full">
                                    <span>お問い合わせ内容<span class="required">*</span></span>
                                    <textarea name="message" rows="8" placeholder="お問い合わせ内容をご記入ください。" required></textarea>
                                </label>
                            </div>
                            <?php
                            $privacy_company_name = $brand['site_name'] ?? 'リアンハート';
                            $privacy_address = '';
                            $privacy_phone = '';
                            $privacy_email = '';
                            if (!empty($company['rows']) && is_array($company['rows'])) {
                                foreach ($company['rows'] as $row) {
                                    $row_label = trim((string) ($row['label'] ?? ''));
                                    $row_value = trim((string) ($row['value'] ?? ''));
                                    if ($row_label === '所在地') {
                                        $privacy_address = $row_value;
                                    } elseif ($row_label === '電話番号') {
                                        $privacy_phone = $row_value;
                                    } elseif ($row_label === 'メール') {
                                        $privacy_email = $row_value;
                                    }
                                }
                            }
                            ?>
                            <details class="privacy-policy-box" id="privacy-policy" tabindex="-1" aria-label="プライバシーポリシー">
                                <summary class="privacy-policy-box__summary">プライバシーポリシー（クリックで展開）</summary>
                                <div class="privacy-policy-box__body">
                                    <p><?php echo esc_html($privacy_company_name); ?>（以下「当社」）は、愛知県全域での老人ホーム紹介、介護施設紹介、入居相談に関するサービスを提供するにあたり、個人情報の保護を重要な責務と考え、以下の方針に基づいて適切に取り扱います。</p>
                                    <p>1. 取得する個人情報</p>
                                    <p>当社は、入居相談、お問い合わせ、見学調整その他のご相談対応にあたり、お名前、電話番号、メールアドレス、お問い合わせ内容その他ご相談に必要な情報を取得することがあります。</p>
                                    <p>2. 利用目的</p>
                                    <p>取得した個人情報は、老人ホーム紹介、介護施設紹介、入居相談への対応、施設候補のご案内、見学日程の調整、比較検討のサポート、本人確認、ご相談内容への回答、ご連絡、サービス品質向上のための確認および改善のために利用します。</p>
                                    <p>3. 第三者提供について</p>
                                    <p>当社は、法令に基づく場合を除き、ご本人またはご家族の同意なく個人情報を第三者へ提供しません。ただし、見学調整や受入可否確認など、ご相談対応に必要な範囲で、候補施設や関係事業者へ情報を共有する場合があります。</p>
                                    <p>4. 安全管理措置</p>
                                    <p>当社は、個人情報の漏えい、滅失、き損、不正アクセス等を防止するため、必要かつ合理的な安全管理措置を講じます。</p>
                                    <p>5. 開示、訂正、利用停止等</p>
                                    <p>ご本人から、自己の個人情報について開示、訂正、追加、削除、利用停止等の請求があった場合は、法令に従い適切に対応します。</p>
                                    <p>6. Cookie等について</p>
                                    <p>当サイトでは、利便性向上や利用状況の把握のために、Cookie 等の技術を使用する場合があります。これにより個人を直接特定する情報を取得するものではありません。</p>
                                    <p>7. お問い合わせ窓口</p>
                                    <p>個人情報の取扱いに関するお問い合わせは、以下までご連絡ください。</p>
                                    <p>【Webの場合】<br>お問い合わせフォームよりお問い合わせください。</p>
                                    <p>【郵送の場合】<br>所在地：<?php echo esc_html($privacy_address !== '' ? $privacy_address : '〒460-0000 愛知県名古屋市中区○○1-2-3 ○○ビル5F'); ?><br><?php echo esc_html($privacy_company_name); ?><?php if ($privacy_phone !== '') : ?><br>TEL：<?php echo esc_html($privacy_phone); ?><?php endif; ?><?php if ($privacy_email !== '') : ?><br>MAIL：<?php echo esc_html($privacy_email); ?><?php endif; ?></p>
                                    <p>8. 改定</p>
                                    <p>本ポリシーは、法令改正や運用見直しに応じて改定することがあります。改定後の内容は、本サイトに掲載した時点で効力を生じます。</p>
                                </div>
                            </details>
                            <label class="contact-field contact-field--privacy">
                                <input type="checkbox" name="privacy" value="1" required>
                                <span><a href="#privacy-policy">プライバシーポリシー</a>に同意する<span class="required">*</span></span>
                            </label>
                            <input class="contact-form__honeypot" type="text" name="website" tabindex="-1" autocomplete="off">
                            <input type="hidden" name="source_url" value="">
                            <div class="contact-form__actions">
                                <div class="form-status" aria-live="polite"></div>
                                <div class="contact-form__submit-wrap">
                                    <button class="contact-form__submit" type="submit">確認する</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
