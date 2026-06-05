<?php
include_once(dirname(__FILE__).'/_init.php');

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', 'Maganda TV') : 'Maganda TV';
$api_url = G5_PLUGIN_URL . '/maganda/api/application.php';
$thanks_url = G5_URL . '/page/creator-apply-thanks.php';

g5_page_start('Creator Application', 'minimal');
?>
<div class="page-template page-creator-apply">
    <header class="page-hero reveal">
        <div class="page-inner">
            <p class="page-eyebrow">Creator Program</p>
            <h1 class="page-title">Become a Creator</h1>
            <p class="page-desc">Streaming on YouTube? Apply to join <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?> as a live creator and connect with supporters worldwide.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner page-inner--narrow">
            <form id="creatorApplyForm" class="page-form" method="post" action="#" novalidate>
                <div class="page-form__row">
                    <label for="ca_name">Full Name <span class="page-form__req" aria-hidden="true">*</span></label>
                    <input type="text" id="ca_name" name="name" required autocomplete="name" placeholder="Your name">
                </div>
                <div class="page-form__row">
                    <label for="ca_email">Email <span class="page-form__req" aria-hidden="true">*</span></label>
                    <input type="email" id="ca_email" name="email" required autocomplete="email" placeholder="you@example.com">
                </div>
                <div class="page-form__row">
                    <label for="ca_phone">Phone / WhatsApp <span class="page-form__req" aria-hidden="true">*</span></label>
                    <input type="text" id="ca_phone" name="phone" required autocomplete="tel" placeholder="+63 9XX XXX XXXX">
                </div>
                <div class="page-form__row">
                    <label for="ca_youtube">YouTube Channel or Live URL <span class="page-form__req" aria-hidden="true">*</span></label>
                    <input type="url" id="ca_youtube" name="youtube" required placeholder="https://www.youtube.com/...">
                </div>
                <div class="page-form__row">
                    <label for="ca_message">About You &amp; Message</label>
                    <textarea id="ca_message" name="message" rows="6" placeholder="Tell us about your content, streaming schedule, experience, and why you want to join."></textarea>
                </div>
                <p id="creatorApplyMsg" class="page-form__msg" aria-live="polite"></p>
                <button type="submit" class="page-btn page-btn--primary">Submit Application</button>
            </form>
        </div>
    </section>
</div>
<script>
(function () {
    var form = document.getElementById('creatorApplyForm');
    var msg = document.getElementById('creatorApplyMsg');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.textContent = 'Sending…';
        msg.className = 'page-form__msg is-loading';
        var fd = new FormData(form);
        fetch(<?php echo json_encode($api_url, JSON_UNESCAPED_UNICODE); ?>, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.ok) {
                location.href = <?php echo json_encode($thanks_url, JSON_UNESCAPED_UNICODE); ?>;
                return;
            }
            msg.className = 'page-form__msg is-error';
            msg.textContent = data.message || 'Application failed. Please try again.';
        }).catch(function () {
            msg.className = 'page-form__msg is-error';
            msg.textContent = 'Network error. Please check your connection and try again.';
        });
    });
})();
</script>
<?php g5_page_end('minimal'); ?>
