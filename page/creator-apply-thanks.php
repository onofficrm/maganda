<?php
include_once dirname(__FILE__) . '/_init.php';

if (!function_exists('g5site_cfg') && is_file(G5_PATH . '/_site.config.php')) {
    include_once G5_PATH . '/_site.config.php';
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', 'Maganda TV') : 'Maganda TV';
$home_url = defined('G5_URL') ? G5_URL : '/';

g5_page_start('Application Received', 'minimal');
?>
<div class="page-template page-creator-apply page-creator-apply--thanks">
    <header class="page-hero reveal">
        <div class="page-inner page-inner--narrow">
            <p class="page-eyebrow">Thank You</p>
            <h1 class="page-title">Application Received</h1>
            <p class="page-desc">Thank you for applying to <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?>. Our team will review your submission and contact you by email or phone soon.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner page-inner--narrow">
            <div class="page-cta__actions">
                <a href="<?php echo htmlspecialchars($home_url, ENT_QUOTES, 'UTF-8'); ?>" class="page-btn page-btn--primary">Back to Home</a>
            </div>
        </div>
    </section>
</div>
<?php g5_page_end('minimal'); ?>
