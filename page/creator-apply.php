<?php
include_once(dirname(__FILE__) . '/_init.php');

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', 'Maganda TV') : 'Maganda TV';
$api_url = G5_PLUGIN_URL . '/maganda/api/application.php';
$thanks_url = G5_URL . '/page/creator-apply-thanks.php';

add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/g5b-creator-apply.css">', 3);

g5_page_start('Creator Application', 'minimal');
?>
<div class="mg-creator-apply">
    <div class="mg-creator-apply__bg" aria-hidden="true"></div>
    <div class="mg-creator-apply__inner">
        <a class="mg-creator-apply__brand" href="<?php echo G5_URL; ?>">
            <span class="mg-creator-apply__brand-mark" aria-hidden="true"></span>
            <span><?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>

        <header class="mg-creator-apply__hero">
            <p class="mg-creator-apply__eyebrow">Creator Program</p>
            <h1 class="mg-creator-apply__title">Become a Creator</h1>
            <p class="mg-creator-apply__desc">Stream on YouTube, TikTok, or Instagram Live? Apply to join <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?> and connect with supporters worldwide.</p>
        </header>

        <div class="mg-creator-apply__card">
            <div class="mg-creator-apply__note">
                <span aria-hidden="true">ℹ️</span>
                <span>Add at least one live platform URL below. We will review your channels before approval.</span>
            </div>

            <form id="creatorApplyForm" class="mg-creator-form" method="post" action="#" novalidate>
                <div class="mg-creator-form__grid">
                    <div class="mg-creator-form__row">
                        <label for="ca_name">Full Name <span class="mg-creator-form__req">*</span></label>
                        <input type="text" id="ca_name" name="name" required autocomplete="name" placeholder="Your name">
                    </div>
                    <div class="mg-creator-form__row">
                        <label for="ca_email">Email <span class="mg-creator-form__req">*</span></label>
                        <input type="email" id="ca_email" name="email" required autocomplete="email" placeholder="you@example.com">
                    </div>
                    <div class="mg-creator-form__row mg-creator-form__row--full">
                        <label for="ca_phone">Phone / WhatsApp <span class="mg-creator-form__req">*</span></label>
                        <input type="text" id="ca_phone" name="phone" required autocomplete="tel" placeholder="+63 9XX XXX XXXX">
                    </div>
                </div>

                <fieldset class="mg-creator-platforms">
                    <legend class="mg-creator-platforms__legend">Live Platforms</legend>
                    <p class="mg-creator-platforms__hint">Paste your channel or live URL. At least one platform is required.</p>

                    <div class="mg-creator-platform">
                        <div class="mg-creator-platform__icon mg-creator-platform__icon--yt" aria-hidden="true">YT</div>
                        <div class="mg-creator-platform__body">
                            <label for="ca_youtube">YouTube Channel or Live URL</label>
                            <input type="url" id="ca_youtube" name="youtube" data-platform="youtube" placeholder="https://www.youtube.com/... or youtu.be/...">
                        </div>
                    </div>

                    <div class="mg-creator-platform">
                        <div class="mg-creator-platform__icon mg-creator-platform__icon--tt" aria-hidden="true">TT</div>
                        <div class="mg-creator-platform__body">
                            <label for="ca_tiktok">TikTok Profile or Live URL</label>
                            <input type="url" id="ca_tiktok" name="tiktok" data-platform="tiktok" placeholder="https://www.tiktok.com/@username">
                        </div>
                    </div>

                    <div class="mg-creator-platform">
                        <div class="mg-creator-platform__icon mg-creator-platform__icon--ig" aria-hidden="true">IG</div>
                        <div class="mg-creator-platform__body">
                            <label for="ca_instagram">Instagram Profile URL</label>
                            <input type="url" id="ca_instagram" name="instagram" data-platform="instagram" placeholder="https://www.instagram.com/username">
                        </div>
                    </div>
                </fieldset>

                <div class="mg-creator-preview" id="streamPreview" hidden>
                    <div class="mg-creator-preview__head">
                        <span>Live Preview</span>
                        <span class="mg-creator-preview__badge" id="previewBadge">—</span>
                    </div>
                    <div class="mg-creator-preview__frame" id="previewFrame">
                        <div class="mg-creator-preview__empty" id="previewEmpty">Enter a platform URL to preview your stream.</div>
                    </div>
                    <a class="mg-creator-preview__link" id="previewLink" href="#" target="_blank" rel="noopener noreferrer" hidden>Open on platform</a>
                </div>

                <div class="mg-creator-form__row mg-creator-form__row--full" style="margin-top:1.15rem;">
                    <label for="ca_message">About You &amp; Message</label>
                    <textarea id="ca_message" name="message" rows="5" placeholder="Tell us about your content, streaming schedule, experience, and why you want to join."></textarea>
                </div>

                <p id="creatorApplyMsg" class="mg-creator-form__msg" aria-live="polite"></p>
                <button type="submit" class="mg-creator-form__submit">Submit Application</button>
            </form>
        </div>
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('creatorApplyForm');
    var msg = document.getElementById('creatorApplyMsg');
    var previewWrap = document.getElementById('streamPreview');
    var previewFrame = document.getElementById('previewFrame');
    var previewEmpty = document.getElementById('previewEmpty');
    var previewBadge = document.getElementById('previewBadge');
    var previewLink = document.getElementById('previewLink');
    var platformInputs = form.querySelectorAll('[data-platform]');

    function ytId(u) {
        u = (u || '').trim();
        if (/^[a-zA-Z0-9_-]{11}$/.test(u)) return u;
        var p = [/(?:v=|\/vi\/|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_-]{11})/, /[?&]v=([a-zA-Z0-9_-]{11})/];
        for (var i = 0; i < p.length; i++) { var m = u.match(p[i]); if (m) return m[1]; }
        return '';
    }
    function ttUser(u) {
        u = (u || '').trim();
        var m = u.match(/tiktok\.com\/@([^/?\s&]+)/i);
        return m ? m[1] : '';
    }
    function igUser(u) {
        u = (u || '').trim();
        var m = u.match(/instagram\.com\/([^/?\s#]+)/i);
        if (!m) return '';
        var n = m[1].toLowerCase();
        return ['p', 'reel', 'reels', 'stories', 'tv', 'live', 'explore'].indexOf(n) >= 0 ? '' : m[1];
    }
    function parsePreview(url) {
        url = (url || '').trim();
        if (!url) return null;
        var id = ytId(url);
        if (id) return { platform: 'YouTube', embed: 'https://www.youtube.com/embed/' + id + '?rel=0&modestbranding=1&playsinline=1', watch: url.indexOf('http') === 0 ? url : 'https://www.youtube.com/watch?v=' + id };
        var tt = ttUser(url);
        if (tt) return { platform: 'TikTok', embed: 'https://www.tiktok.com/embed/@' + encodeURIComponent(tt), watch: url.indexOf('tiktok.com') >= 0 ? url : 'https://www.tiktok.com/@' + tt };
        var ig = igUser(url);
        if (ig) return { platform: 'Instagram', embed: 'https://www.instagram.com/' + encodeURIComponent(ig) + '/embed', watch: 'https://www.instagram.com/' + ig + '/' };
        return null;
    }
    function renderPreview() {
        var chosen = null;
        platformInputs.forEach(function (input) {
            if (!chosen && input.value.trim()) chosen = parsePreview(input.value);
        });
        if (!chosen) {
            previewWrap.hidden = true;
            previewFrame.querySelectorAll('iframe').forEach(function (f) { f.remove(); });
            previewEmpty.hidden = false;
            return;
        }
        previewWrap.hidden = false;
        previewBadge.textContent = chosen.platform;
        previewBadge.className = 'mg-creator-preview__badge is-active';
        previewLink.href = chosen.watch;
        previewLink.textContent = 'Open on ' + chosen.platform;
        previewLink.hidden = false;
        previewEmpty.hidden = true;
        previewFrame.querySelectorAll('iframe').forEach(function (f) { f.remove(); });
        var iframe = document.createElement('iframe');
        iframe.src = chosen.embed;
        iframe.title = chosen.platform + ' preview';
        iframe.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
        iframe.allowFullscreen = true;
        previewFrame.appendChild(iframe);
    }
    platformInputs.forEach(function (input) {
        input.addEventListener('input', renderPreview);
        input.addEventListener('change', renderPreview);
    });
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var yt = document.getElementById('ca_youtube').value.trim();
        var tt = document.getElementById('ca_tiktok').value.trim();
        var ig = document.getElementById('ca_instagram').value.trim();
        if (!yt && !tt && !ig) {
            msg.className = 'mg-creator-form__msg is-error';
            msg.textContent = 'Please provide at least one live platform URL.';
            return;
        }
        msg.textContent = 'Sending…';
        msg.className = 'mg-creator-form__msg is-loading';
        fetch(<?php echo json_encode($api_url, JSON_UNESCAPED_UNICODE); ?>, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.ok) {
                location.href = <?php echo json_encode($thanks_url, JSON_UNESCAPED_UNICODE); ?>;
                return;
            }
            msg.className = 'mg-creator-form__msg is-error';
            msg.textContent = data.message || 'Application failed. Please try again.';
        }).catch(function () {
            msg.className = 'mg-creator-form__msg is-error';
            msg.textContent = 'Network error. Please check your connection and try again.';
        });
    });
})();
</script>
<?php g5_page_end('minimal'); ?>
