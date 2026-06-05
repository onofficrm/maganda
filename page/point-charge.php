<?php
include_once(dirname(__FILE__).'/_init.php');

global $is_member, $member;

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$email = function_exists('g5site_cfg') ? g5site_cfg('email', 'help@example.com') : 'help@example.com';
$phone = function_exists('g5site_cfg') ? g5site_cfg('phone', '') : '';
$point_url = G5_BBS_URL . '/point.php';
$login_url = G5_BBS_URL . '/login.php?url=' . urlencode(G5_URL . '/page/point-charge.php');

g5_page_start('포인트 충전');
?>
<div class="page-template page-point-charge">
    <header class="page-hero reveal">
        <div class="page-inner">
            <p class="page-eyebrow">Points</p>
            <h1 class="page-title">포인트 충전</h1>
            <p class="page-desc">크리에이터에게 선물할 포인트를 충전합니다.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner page-content">
            <?php if ($is_member) { ?>
                <p><strong><?php echo get_text($member['mb_nick']); ?></strong>님 보유 포인트: <strong><?php echo number_format((int) $member['mb_point']); ?>P</strong></p>
                <p>온라인 결제 연동 전까지는 아래 방법으로 충전 요청을 접수해 주세요.</p>
            <?php } else { ?>
                <p>포인트 충전은 회원 로그인 후 이용 가능합니다.</p>
                <p><a class="page-btn page-btn--primary" href="<?php echo $login_url; ?>">로그인 후 충전하기</a></p>
            <?php } ?>
            <h2>충전 안내</h2>
            <ul class="page-list">
                <li>1P = 1원 기준으로 선물 후원에 사용됩니다.</li>
                <li>충전 요청: <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?><?php if ($phone !== '') { ?> / <?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?><?php } ?></li>
                <li>충전 완료 후 <a href="<?php echo $point_url; ?>">포인트 내역</a>에서 확인할 수 있습니다.</li>
            </ul>
            <?php if ($is_member) { ?>
                <p><a class="page-btn page-btn--outline" href="<?php echo G5_URL; ?>/page/contact.php">충전 문의하기</a></p>
            <?php } ?>
        </div>
    </section>
</div>
<?php g5_page_end(); ?>
