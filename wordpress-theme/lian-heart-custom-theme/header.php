<?php
if (!defined('ABSPATH')) {
    exit;
}

$theme = lh_theme_data();
$brand = $theme['brand'];
$nav_items = array(
    '#concept' => 'コンセプト',
    '#pride' => 'サービスの特徴',
    '#menu' => '入居相談の流れ',
    '#greeting' => '代表挨拶',
    '#qa' => 'よくある質問',
    '#facility' => '施設の種類',
    '#shop-info' => '運営会社',
);
$logo = lh_resolve_image($brand['logo'] ?? null, $brand['site_name'], 320, 320);
$header_cta = $brand['header_cta'] ?? array('label' => '入居相談', 'url' => '#contact', 'style' => 'primary');
if (is_array($header_cta)) {
    $header_cta['style'] = 'primary';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('lh-theme'); ?>>
<?php wp_body_open(); ?>
<header class="g-header" id="top">
    <div class="g-header__inner">
        <a class="g-header__logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($brand['site_name']); ?>">
            <span class="g-header__logo-mark">
                <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
            </span>
            <span class="g-header__logo-texts">
                <strong><?php echo esc_html($brand['site_name']); ?></strong>
                <small><?php echo esc_html($brand['tagline']); ?></small>
            </span>
        </a>
        <button class="hamburger-btn" type="button" aria-expanded="false" aria-controls="site-navigation">
            <span></span>
            <span></span>
            <span></span>
            <span class="screen-reader-text">メニューを開く</span>
        </button>
        <div class="g-header__nav-block" id="site-navigation">
            <nav class="g-header__nav" aria-label="グローバルナビゲーション">
                <?php foreach ($nav_items as $href => $label) : ?>
                    <a href="<?php echo esc_url($href); ?>"><?php echo esc_html($label); ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="g-header__cta">
                <?php echo lh_render_button(is_array($header_cta) ? $header_cta : array('label' => 'お問い合わせ', 'url' => '#contact', 'style' => 'primary'), 'g-header__cta-link'); ?>
            </div>
        </div>
    </div>
</header>
