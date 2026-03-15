<?php
if (!defined('ABSPATH')) {
    exit;
}

$theme = lh_theme_data();
$brand = $theme['brand'];
$floating_ctas = array_slice($theme['hero']['ctas'], 0, 2);
$footer_nav = array(
    '#concept' => 'コンセプト',
    '#pride' => 'サービスの特徴',
    '#menu' => '入居相談の流れ',
    '#greeting' => '代表挨拶',
    '#qa' => 'よくある質問',
    '#facility' => '施設の種類',
    '#shop-info' => '運営会社',
    '#contact' => 'お問い合わせ',
    '#privacy-policy' => 'プライバシーポリシー',
);
?>
<?php if (!empty($floating_ctas)) : ?>
    <div class="floating-btns" aria-label="固定導線">
        <?php foreach ($floating_ctas as $cta) : ?>
            <?php echo lh_render_button($cta, 'floating-btns__item'); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<button class="page-top" type="button" aria-label="ページ上部へ戻る">
    <span></span>
</button>
<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="site-footer__brand">
            <strong><?php echo esc_html($brand['site_name']); ?></strong>
            <p><?php echo esc_html($brand['footer_note']); ?></p>
        </div>
        <nav class="site-footer__nav" aria-label="フッターナビゲーション">
            <?php foreach ($footer_nav as $href => $label) : ?>
                <a href="<?php echo esc_url($href); ?>"><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>
        <p class="site-footer__copy"><?php echo esc_html($brand['copyright']); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
