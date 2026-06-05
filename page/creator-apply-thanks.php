<?php
include_once dirname(__FILE__) . '/_init.php';

if (!function_exists('g5site_cfg') && is_file(G5_PATH . '/_site.config.php')) {
    include_once G5_PATH . '/_site.config.php';
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', 'Maganda TV') : 'Maganda TV';
$home_url = defined('G5_URL') ? G5_URL : '/';

add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/g5b-creator-apply.css">', 3);

g5_page_start('Application Received', 'minimal');
?>
<div class="mg-creator-apply mg-creator-apply--thanks">
    <div class="mg-creator-apply__bg" aria-hidden="true"></div>
    <div class="mg-creator-apply__inner">
        <a class="mg-creator-apply__brand" href="<?php echo htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>">
            <span class="mg-creator-apply__brand-mark" aria-hidden="true"></span>
            <span><?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
        <header class="mg-creator-apply__hero">
            <p class="mg-creator-apply__eyebrow">Thank You</p>
            <h1 class="mg-creator-apply__title">Application Received</h1>
            <p class="mg-creator-apply__desc">Thank you for applying to <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?>. Our team will review your channels and contact you soon.</p>
        </header>
        <div class="mg-creator-apply__card">
            <div class="mg-creator-apply__actions">
                <a href="<?php echo htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>" class="mg-creator-form__submit">Back to Home</a>
            </div>
        </div>
    </div>
</div>
<?php g5_page_end('minimal'); ?>
