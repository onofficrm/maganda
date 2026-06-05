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
                        <div class="mg-creator-field">
                            <input type="text" id="ca_name" name="name" required autocomplete="name" placeholder="Your name">
                        </div>
                    </div>
                    <div class="mg-creator-form__row">
                        <label for="ca_email">Email <span class="mg-creator-form__req">*</span></label>
                        <div class="mg-creator-field">
                            <input type="email" id="ca_email" name="email" required autocomplete="email" placeholder="you@example.com">
                        </div>
                    </div>
                    <div class="mg-creator-form__row mg-creator-form__row--full">
                        <label for="ca_phone">Phone / WhatsApp <span class="mg-creator-form__req">*</span></label>
                        <div class="mg-creator-field">
                            <input type="text" id="ca_phone" name="phone" required autocomplete="tel" placeholder="+63 9XX XXX XXXX">
                        </div>
                    </div>
                </div>

                <fieldset class="mg-creator-platforms">
                    <legend class="mg-creator-platforms__legend">Live Platforms</legend>
                    <p class="mg-creator-platforms__hint">Paste your channel or live URL. At least one platform is required.</p>

                    <div class="mg-creator-platform mg-creator-platform--youtube">
                        <label for="ca_youtube" class="mg-creator-platform__label">YouTube</label>
                        <div class="mg-creator-platform__input-wrap">
                            <span class="mg-creator-platform__logo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="6" fill="#FF0000"/><path d="M10 8.5v7l5.5-3.5L10 8.5z" fill="#fff"/></svg>
                            </span>
                            <input type="url" id="ca_youtube" name="youtube" class="mg-creator-platform__input" data-platform="youtube" placeholder="youtube.com/... or youtu.be/..." autocomplete="url">
                        </div>
                    </div>

                    <div class="mg-creator-platform mg-creator-platform--tiktok">
                        <label for="ca_tiktok" class="mg-creator-platform__label">TikTok</label>
                        <div class="mg-creator-platform__input-wrap">
                            <span class="mg-creator-platform__logo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="6" fill="#010101"/><path d="M16.5 8.2a3.4 3.4 0 0 0 2.2-2.2H15v8.8a2.1 2.1 0 1 1-2.1-2.1c.4 0 .8.1 1.1.3V12a3.6 3.6 0 1 0 3.1 3.6V9.8c.8.6 1.8.9 2.9.9V8.4c-1 0-2-.4-2.7-1.2z" fill="#25F4EE"/><path d="M16.5 7.8a3.4 3.4 0 0 0 2.2-2.2H15v8.8a2.1 2.1 0 1 1-2.1-2.1c.4 0 .8.1 1.1.3V11.6a3.6 3.6 0 1 0 3.1 3.6V9.4c.8.6 1.8.9 2.9.9V7c-1 0-2-.4-2.7-1.2z" fill="#FE2C55"/></svg>
                            </span>
                            <input type="url" id="ca_tiktok" name="tiktok" class="mg-creator-platform__input" data-platform="tiktok" placeholder="tiktok.com/@username" autocomplete="url">
                        </div>
                    </div>

                    <div class="mg-creator-platform mg-creator-platform--instagram">
                        <label for="ca_instagram" class="mg-creator-platform__label">Instagram</label>
                        <div class="mg-creator-platform__input-wrap">
                            <span class="mg-creator-platform__logo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="mgIgGrad" x1="4" y1="22" x2="22" y2="4" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5949"/><stop offset=".5" stop-color="#D6249F"/><stop offset="1" stop-color="#285AEB"/></linearGradient></defs><rect width="24" height="24" rx="6" fill="url(#mgIgGrad)"/><rect x="7" y="7" width="10" height="10" rx="3" stroke="#fff" stroke-width="1.5"/><circle cx="17.2" cy="6.8" r="1.1" fill="#fff"/><circle cx="12" cy="12" r="2.4" stroke="#fff" stroke-width="1.5"/></svg>
                            </span>
                            <input type="url" id="ca_instagram" name="instagram" class="mg-creator-platform__input" data-platform="instagram" placeholder="instagram.com/username" autocomplete="url">
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

                <div class="mg-creator-form__row mg-creator-form__row--full mg-creator-form__row--message">
                    <label for="ca_message">About You &amp; Message</label>
                    <div class="mg-creator-field">
                        <textarea id="ca_message" name="message" rows="5" placeholder="Tell us about your content, streaming schedule, experience, and why you want to join."></textarea>
                    </div>
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
