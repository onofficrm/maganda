<?php
include_once(dirname(__FILE__).'/_init.php');

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$api_url = G5_PLUGIN_URL . '/maganda/api/application.php';
$thanks_url = G5_URL . '/page/inquiry-thanks.php';

g5_page_start('방송회원 신청');
?>
<div class="page-template page-creator-apply">
    <header class="page-hero reveal">
        <div class="page-inner">
            <p class="page-eyebrow">Creator</p>
            <h1 class="page-title">방송회원 신청</h1>
            <p class="page-desc">YouTube 라이브를 운영 중이라면 <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?> 방송회원으로 신청해 주세요.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner">
            <form id="creatorApplyForm" class="page-form" method="post" action="#" novalidate>
                <div class="page-form__row">
                    <label for="ca_name">이름 *</label>
                    <input type="text" id="ca_name" name="name" required>
                </div>
                <div class="page-form__row">
                    <label for="ca_email">이메일 *</label>
                    <input type="email" id="ca_email" name="email" required>
                </div>
                <div class="page-form__row">
                    <label for="ca_phone">연락처 *</label>
                    <input type="text" id="ca_phone" name="phone" required>
                </div>
                <div class="page-form__row">
                    <label for="ca_youtube">YouTube 채널/라이브 URL *</label>
                    <input type="url" id="ca_youtube" name="youtube" required placeholder="https://www.youtube.com/...">
                </div>
                <div class="page-form__row">
                    <label for="ca_message">소개 및 문의</label>
                    <textarea id="ca_message" name="message" rows="6" placeholder="방송 주제, 운영 시간, 경력 등을 적어 주세요."></textarea>
                </div>
                <p id="creatorApplyMsg" class="page-form__msg" aria-live="polite"></p>
                <button type="submit" class="page-btn page-btn--primary">신청하기</button>
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
        msg.textContent = '전송 중...';
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
            msg.textContent = data.message || '신청에 실패했습니다.';
        }).catch(function () {
            msg.textContent = '네트워크 오류가 발생했습니다.';
        });
    });
})();
</script>
<?php g5_page_end(); ?>
