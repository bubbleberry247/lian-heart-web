<?php
/**
 * Template Name: Referrer Guide
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

the_post();

$definition = lh_referrer_page_definition();
$page_title = get_the_title();
if ($page_title === '') {
    $page_title = $definition['title'] ?? 'For Professionals';
}

$lead = trim((string) ($definition['lead'] ?? ''));
$summary = trim((string) ($definition['summary'] ?? ''));
$home_label = trim((string) ($definition['breadcrumb_home_label'] ?? 'Home'));
$policy_title = trim((string) ($definition['policy_title'] ?? 'Policy'));
$principles = (array) ($definition['principles'] ?? array());
$flow_title = trim((string) ($definition['flow_title'] ?? 'Flow'));
$flow = (array) ($definition['flow'] ?? array());
$cta_text = trim((string) ($definition['cta_text'] ?? ''));
$cta = is_array($definition['cta'] ?? null) ? $definition['cta'] : array();
$content = trim((string) get_the_content());
?>
<main class="site-main referrer-page">
    <section class="section referrer-page__hero">
        <div class="constrained-content">
            <nav class="referrer-page__breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($home_label); ?></a>
                <span aria-hidden="true">/</span>
                <span><?php echo esc_html($page_title); ?></span>
            </nav>
            <?php echo lh_render_headline($definition['en_label'] ?? 'Support', $page_title, array('section', 'referrer')); ?>
            <?php if ($lead !== '') : ?>
                <p class="referrer-page__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <?php if ($summary !== '') : ?>
                <p class="referrer-page__summary"><?php echo esc_html($summary); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section referrer-page__policy">
        <div class="constrained-content">
            <?php echo lh_render_headline('Policy', $policy_title, array('section', 'referrer-policy')); ?>
            <div class="referrer-page__grid">
                <?php foreach ($principles as $item) : ?>
                    <?php
                    $title = trim((string) ($item['title'] ?? ''));
                    $body = trim((string) ($item['body'] ?? ''));
                    if ($title === '' && $body === '') {
                        continue;
                    }
                    ?>
                    <article class="referrer-card js-knowledge-card-fx">
                        <?php if ($title !== '') : ?>
                            <h3 class="referrer-card__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                        <?php if ($body !== '') : ?>
                            <p class="referrer-card__body"><?php echo esc_html($body); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section referrer-page__flow">
        <div class="constrained-content">
            <?php echo lh_render_headline('Flow', $flow_title, array('section', 'referrer-flow')); ?>
            <div class="referrer-flow">
                <?php foreach ($flow as $item) : ?>
                    <?php
                    $title = trim((string) ($item['title'] ?? ''));
                    $body = trim((string) ($item['body'] ?? ''));
                    if ($title === '' && $body === '') {
                        continue;
                    }
                    ?>
                    <article class="referrer-flow__item js-knowledge-card-fx">
                        <?php if ($title !== '') : ?>
                            <h3 class="referrer-flow__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>
                        <?php if ($body !== '') : ?>
                            <p class="referrer-flow__body"><?php echo esc_html($body); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($content !== '') : ?>
                <article class="referrer-page__content">
                    <?php the_content(); ?>
                </article>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($cta['label'])) : ?>
        <section class="section referrer-page__cta">
            <div class="constrained-content">
                <div class="referrer-page__cta-card js-knowledge-card-fx">
                    <?php if ($cta_text !== '') : ?>
                        <p class="referrer-page__cta-text"><?php echo esc_html($cta_text); ?></p>
                    <?php endif; ?>
                    <?php echo lh_render_button($cta, 'referrer-page__cta-action'); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
