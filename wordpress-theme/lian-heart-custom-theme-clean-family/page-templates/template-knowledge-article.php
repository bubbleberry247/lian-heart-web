<?php
/**
 * Template Name: Knowledge Article
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

the_post();
$definitions = lh_knowledge_article_definitions();
$current_slug = get_post_field('post_name', get_the_ID());
$current_definition = $definitions[$current_slug] ?? array();

$en_label = function_exists('get_field') ? get_field('lh_article_en_label') : '';
$lead = function_exists('get_field') ? get_field('lh_article_lead') : '';
$hero_image = function_exists('get_field') ? get_field('lh_article_hero_image') : null;
$cta_title = function_exists('get_field') ? get_field('lh_article_cta_title') : '';
$cta_body = function_exists('get_field') ? get_field('lh_article_cta_body') : '';

$en_label = lh_fill_empty($en_label, $current_definition['en_label'] ?? 'Knowledge');
$lead = lh_fill_empty($lead, $current_definition['lead'] ?? '');
$cta_title = lh_fill_empty($cta_title, $current_definition['cta_title'] ?? 'ご相談をご希望の方へ');
$cta_body = lh_fill_empty($cta_body, $current_definition['cta_body'] ?? 'ご本人の状態やご家族のご希望を整理しながら、比較しやすい形で候補をご案内します。');

$hero = lh_resolve_image(lh_fill_empty($hero_image, $current_definition['hero_image'] ?? null), get_the_title(), 1600, 900);
$contact_actions = array(
    array('label' => '相談してみる', 'url' => home_url('/#contact'), 'style' => 'primary'),
);
$contact_phone = lh_get_company_row('電話番号');
if (!lh_is_placeholder_value($contact_phone)) {
    $contact_actions[] = array('label' => '電話で相談する', 'url' => 'tel:' . preg_replace('/[^\d+]/', '', $contact_phone), 'style' => 'line');
}

$editorial_page = get_page_by_path('about-editorial-policy');
$editorial_url = ($editorial_page instanceof WP_Post && $editorial_page->post_status === 'publish')
    ? get_permalink($editorial_page)
    : '';
$published_date = get_the_date('Y.m.d');
$modified_date = get_the_modified_date('Y.m.d');

$sources_raw = (string) get_post_meta(get_the_ID(), 'lh_article_sources', true);
$source_items = array();
foreach (preg_split('/\r\n|\r|\n/', $sources_raw) as $source_line) {
    $source_line = trim($source_line);
    if ($source_line === '') {
        continue;
    }

    $source_parts = explode('｜', $source_line, 2);
    $source_items[] = array(
        'label' => trim($source_parts[0]),
        'url'   => isset($source_parts[1]) ? trim($source_parts[1]) : '',
    );
}

$related_articles = array();
foreach ($definitions as $slug => $definition) {
    if ($slug === $current_slug) {
        continue;
    }

    $related_page = get_page_by_path($slug);
    if (!($related_page instanceof WP_Post) || $related_page->post_status !== 'publish') {
        continue;
    }

    $related_articles[] = array(
        'title' => get_the_title($related_page),
        'url' => get_permalink($related_page),
        'body' => $definition['card_body'] ?? '',
    );

    if (count($related_articles) >= 3) {
        break;
    }
}
?>
<main class="site-main knowledge-article-page">
    <section class="knowledge-article-hero">
        <div class="knowledge-article-hero__inner">
            <nav class="knowledge-article-breadcrumb" aria-label="パンくずリスト">
                <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url(lh_resolve_anchor_url('#knowledge')); ?>">入居前に知っておきたいこと</a>
                <span aria-hidden="true">/</span>
                <span><?php echo esc_html(get_the_title()); ?></span>
            </nav>

            <div class="wp-headline wp-headline--section wp-headline--knowledge-article js-headline-fx knowledge-article-hero__headline">
                <p class="wp-headline__alphabetic"><?php echo esc_html(strtoupper($en_label)); ?></p>
                <h1 class="wp-block-heading wp-headline__kana"><?php echo esc_html(get_the_title()); ?></h1>
            </div>

            <?php if ($lead !== '') : ?>
                <p class="knowledge-article-hero__lead js-knowledge-article-fx"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>

            <p class="knowledge-article-meta">
                <span>編集: <?php if ($editorial_url !== '') : ?><a href="<?php echo esc_url($editorial_url); ?>">リアンハート編集部</a><?php else : ?>リアンハート編集部<?php endif; ?></span>
                <span aria-hidden="true">｜</span>
                <span>公開日 <?php echo esc_html($published_date); ?></span>
                <?php if ($modified_date !== $published_date) : ?>
                    <span aria-hidden="true">｜</span>
                    <span>最終更新 <?php echo esc_html($modified_date); ?></span>
                <?php endif; ?>
            </p>

            <figure class="knowledge-article-hero__visual js-knowledge-article-fx">
                <span class="image-wipe" aria-hidden="true"></span>
                <img src="<?php echo esc_url($hero['url']); ?>" alt="<?php echo esc_attr($hero['alt']); ?>">
            </figure>
        </div>
    </section>

    <section class="knowledge-article-main">
        <div class="knowledge-article-main__inner">
            <article class="knowledge-article__content js-knowledge-article-content-fx">
                <?php the_content(); ?>
            </article>

            <?php if (!empty($source_items)) : ?>
                <section class="knowledge-article-sources">
                    <h2>出典</h2>
                    <ul>
                        <?php foreach ($source_items as $source_item) : ?>
                            <li>
                                <?php if ($source_item['url'] !== '') : ?>
                                    <a href="<?php echo esc_url($source_item['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($source_item['label']); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html($source_item['label']); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <aside class="knowledge-article-cta js-knowledge-article-cta-fx">
                <div class="knowledge-article-cta__body">
                    <h2><?php echo esc_html($cta_title); ?></h2>
                    <p><?php echo esc_html($cta_body); ?></p>
                </div>
                <div class="knowledge-article-cta__actions">
                    <?php foreach ($contact_actions as $action) : ?>
                        <?php echo lh_render_button($action, 'knowledge-article-cta__action'); ?>
                    <?php endforeach; ?>
                </div>
                <p class="knowledge-article-cta__disclosure">※ご相談者さまから料金はいただきません。ご入居が決まった場合に施設から紹介手数料を受け取る場合があります（<a href="<?php echo esc_url(home_url('/fees-disclosure/')); ?>">手数料の開示</a>）。当社がすべての施設を紹介できるわけではありません（<a href="<?php echo esc_url(home_url('/fees-disclosure/')); ?>">紹介範囲について</a>）。ご相談内容は施設への打診に必要な範囲で利用します（<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">個人情報の取り扱い</a>）。</p>
            </aside>

            <?php if (!empty($related_articles)) : ?>
                <section class="knowledge-article-related">
                    <h2 class="knowledge-article-related__title">あわせて読みたい記事</h2>
                    <div class="knowledge-article-related__grid">
                        <?php foreach ($related_articles as $related_article) : ?>
                            <article class="knowledge-article-related__item js-knowledge-article-related-fx">
                                <h3><?php echo esc_html($related_article['title']); ?></h3>
                                <?php if (!empty($related_article['body'])) : ?>
                                    <p><?php echo esc_html($related_article['body']); ?></p>
                                <?php endif; ?>
                                <a href="<?php echo esc_url($related_article['url']); ?>">詳しく見る</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
