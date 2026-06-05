<?php
include_once dirname(__FILE__) . '/_init.php';

global $is_member, $member;

if (!function_exists('g5site_cfg') && is_file(G5_PATH . '/_site.config.php')) {
    include_once G5_PATH . '/_site.config.php';
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$api_url = G5_PLUGIN_URL . '/maganda/api/point-charge.php';
$thanks_url = G5_URL . '/page/point-charge-thanks.php';
$login_url = G5_BBS_URL . '/login.php?url=' . urlencode(G5_URL . '/page/point-charge.php');
$point_url = G5_BBS_URL . '/point.php';

$bank = function_exists('maganda_get_bank_settings') ? maganda_get_bank_settings() : array(
    'bank_name' => '',
    'bank_account' => '',
    'bank_holder' => '',
    'bank_guide' => '',
    'point_min_amount' => 1000,
);
$charges = ($is_member && function_exists('maganda_get_member_point_charges'))
    ? maganda_get_member_point_charges($member['mb_id'], 8)
    : array();
$bank_ready = $bank['bank_name'] !== '' && $bank['bank_account'] !== '';
$copy_text = trim($bank['bank_name'] . ' ' . $bank['bank_account'] . ' ' . $bank['bank_holder']);

add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/g5b-point-charge.css">', 3);

g5_page_start('포인트 충전', 'minimal');
?>
<div class="mg-point-charge">
    <div class="mg-point-charge__bg" aria-hidden="true"></div>
    <div class="mg-point-charge__inner">
        <a class="mg-point-charge__brand" href="<?php echo G5_URL; ?>">
            <span class="mg-point-charge__brand-mark" aria-hidden="true"></span>
            <span><?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>

        <header class="mg-point-charge__hero">
            <p class="mg-point-charge__eyebrow">Points</p>
            <h1 class="mg-point-charge__title">포인트 충전</h1>
            <p class="mg-point-charge__desc">크리에이터에게 선물할 포인트를 계좌입금으로 충전합니다. 입금 전·후 모두 신청할 수 있습니다.</p>
        </header>

        <div class="mg-point-charge__card">
            <?php if ($is_member) { ?>
                <div class="mg-point-charge__balance">
                    <span class="mg-point-charge__balance-label"><?php echo get_text($member['mb_nick']); ?>님 보유 포인트</span>
                    <span class="mg-point-charge__balance-value"><?php echo number_format((int) $member['mb_point']); ?>P</span>
                </div>
            <?php } ?>

            <ol class="mg-point-charge__steps">
                <li><span class="mg-point-charge__steps-num">1</span><span>충전 포인트와 입금자명을 입력해 신청합니다. (입금 전·후 순서 무관)</span></li>
                <li><span class="mg-point-charge__steps-num">2</span><span>아래 계좌로 1P = 1원 기준 입금합니다.</span></li>
                <li><span class="mg-point-charge__steps-num">3</span><span>관리자가 입금 확인 후 포인트가 충전됩니다.</span></li>
            </ol>

            <?php if ($bank_ready) { ?>
            <div class="mg-point-charge__bank">
                <h2 class="mg-point-charge__bank-title">입금 계좌</h2>
                <div class="mg-point-charge__bank-row">
                    <span class="mg-point-charge__bank-label">은행</span>
                    <span class="mg-point-charge__bank-value"><?php echo get_text($bank['bank_name']); ?></span>
                </div>
                <div class="mg-point-charge__bank-row">
                    <span class="mg-point-charge__bank-label">계좌번호</span>
                    <span class="mg-point-charge__bank-value mg-point-charge__bank-value--account" id="pcBankAccount"><?php echo get_text($bank['bank_account']); ?></span>
                </div>
                <div class="mg-point-charge__bank-row">
                    <span class="mg-point-charge__bank-label">예금주</span>
                    <span class="mg-point-charge__bank-value"><?php echo get_text($bank['bank_holder']); ?></span>
                </div>
                <button type="button" class="mg-point-charge__copy" id="pcCopyBtn" data-copy="<?php echo htmlspecialchars($copy_text, ENT_QUOTES, 'UTF-8'); ?>">계좌번호 복사</button>
            </div>
            <?php } else { ?>
            <div class="mg-point-charge__guide">입금 계좌 정보가 아직 등록되지 않았습니다. 잠시 후 다시 확인해 주세요.</div>
            <?php } ?>

            <?php if ($bank['bank_guide'] !== '') { ?>
            <div class="mg-point-charge__guide"><?php echo nl2br(get_text($bank['bank_guide'])); ?></div>
            <?php } ?>

            <?php if (!$is_member) { ?>
            <div class="mg-point-charge__login">
                <p>포인트 충전 신청은 회원 로그인 후 이용 가능합니다.</p>
                <a class="mg-point-charge__login-btn" href="<?php echo $login_url; ?>">로그인 후 충전하기</a>
            </div>
            <?php } elseif ($bank_ready) { ?>
            <form id="pointChargeForm" class="mg-point-charge-form" method="post" action="#" novalidate>
                <div class="mg-point-charge-form__row">
                    <label for="pc_amount">충전 포인트 <span class="mg-point-charge-form__req">*</span></label>
                    <div class="mg-point-charge-form__field">
                        <input type="number" id="pc_amount" name="amount" required min="<?php echo (int) $bank['point_min_amount']; ?>" step="100" placeholder="<?php echo number_format((int) $bank['point_min_amount']); ?>">
                    </div>
                    <p class="mg-point-charge-form__hint">최소 <?php echo number_format((int) $bank['point_min_amount']); ?>P · 1P = 1원</p>
                </div>
                <div class="mg-point-charge-form__row">
                    <label for="pc_depositor">입금자명 <span class="mg-point-charge-form__req">*</span></label>
                    <div class="mg-point-charge-form__field">
                        <input type="text" id="pc_depositor" name="depositor" required maxlength="64" placeholder="입금 시 사용할 이름">
                    </div>
                    <p class="mg-point-charge-form__hint">실제 입금자명과 동일하게 입력해 주세요.</p>
                </div>
                <div class="mg-point-charge-form__row">
                    <label for="pc_memo">메모 (선택)</label>
                    <div class="mg-point-charge-form__field">
                        <textarea id="pc_memo" name="memo" rows="2" maxlength="255" placeholder="입금 예정일 등 참고 사항"></textarea>
                    </div>
                </div>
                <button type="submit" class="mg-point-charge-form__submit">충전 신청하기</button>
                <p id="pointChargeMsg" class="mg-point-charge-form__msg" role="status" aria-live="polite"></p>
            </form>
            <?php } ?>

            <?php if ($is_member && !empty($charges)) { ?>
            <div class="mg-point-charge__history">
                <h2 class="mg-point-charge__history-title">최근 충전 신청</h2>
                <table class="mg-point-charge__history-table">
                    <thead>
                        <tr>
                            <th>신청일</th>
                            <th>포인트</th>
                            <th>입금자</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($charges as $row) {
                        $status_class = 'mg-point-charge__status--' . preg_replace('/[^a-z]/', '', $row['mp_status']);
                        ?>
                        <tr>
                            <td><?php echo get_text(substr($row['mp_datetime'], 0, 10)); ?></td>
                            <td><?php echo number_format((int) $row['mp_amount']); ?>P</td>
                            <td><?php echo get_text($row['mp_depositor']); ?></td>
                            <td><span class="mg-point-charge__status <?php echo $status_class; ?>"><?php echo get_text(maganda_point_charge_status_label($row['mp_status'])); ?></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

            <p class="mg-point-charge__links">
                <a href="<?php echo $point_url; ?>">포인트 내역 보기</a>
            </p>
        </div>
    </div>
</div>
<script>
(function () {
    var copyBtn = document.getElementById('pcCopyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var text = copyBtn.getAttribute('data-copy') || '';
            if (!text) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    copyBtn.textContent = '복사되었습니다';
                    setTimeout(function () { copyBtn.textContent = '계좌번호 복사'; }, 2000);
                });
                return;
            }
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); copyBtn.textContent = '복사되었습니다'; } catch (e) {}
            document.body.removeChild(ta);
            setTimeout(function () { copyBtn.textContent = '계좌번호 복사'; }, 2000);
        });
    }

    var form = document.getElementById('pointChargeForm');
    var msg = document.getElementById('pointChargeMsg');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.textContent = '신청 중…';
        msg.className = 'mg-point-charge-form__msg is-loading';

        fetch(<?php echo json_encode($api_url, JSON_UNESCAPED_UNICODE); ?>, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.ok) {
                location.href = <?php echo json_encode($thanks_url, JSON_UNESCAPED_UNICODE); ?>;
                return;
            }
            msg.className = 'mg-point-charge-form__msg is-error';
            msg.textContent = data.message || '신청에 실패했습니다. 다시 시도해 주세요.';
        }).catch(function () {
            msg.className = 'mg-point-charge-form__msg is-error';
            msg.textContent = '네트워크 오류입니다. 연결을 확인해 주세요.';
        });
    });
})();
</script>
<?php g5_page_end('minimal'); ?>
