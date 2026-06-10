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
    array('label' => '電話で相談する', 'url' => 'tel:052-000-0000', 'style' => 'line'),
);

$related_articles = array();
foreach ($definitions as $slug => $definition) {
    if ($slug === $current_slug) {
        continue;
    }

    $related_page = get_page_by_path($slug);
    $related_articles[] = array(
        'title' => $related_page ? get_the_title($related_page) : ($definition['title'] ?? ''),
        'url' => $related_page ? get_permalink($related_page) : ($definition['url'] ?? home_url('/#knowledge')),
        'body' => $definition['card_body'] ?? '',
    );
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
