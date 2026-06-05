<?php
include_once dirname(__FILE__) . '/_init.php';

if (!function_exists('g5site_cfg') && is_file(G5_PATH . '/_site.config.php')) {
    include_once G5_PATH . '/_site.config.php';
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$charge_url = G5_URL . '/page/point-charge.php';
$point_url = G5_BBS_URL . '/point.php';

add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/g5b-point-charge.css">', 3);

g5_page_start('충전 신청 완료', 'minimal');
?>
<div class="mg-point-charge mg-point-charge--thanks">
    <div class="mg-point-charge__bg" aria-hidden="true"></div>
    <div class="mg-point-charge__inner">
        <a class="mg-point-charge__brand" href="<?php echo G5_URL; ?>">
            <span class="mg-point-charge__brand-mark" aria-hidden="true"></span>
            <span><?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
        <header class="mg-point-charge__hero">
            <p class="mg-point-charge__eyebrow">Thank You</p>
            <h1 class="mg-point-charge__title">충전 신청 완료</h1>
            <p class="mg-point-charge__desc">신청이 접수되었습니다. 입금 후 관리자 확인이 완료되면 포인트가 충전됩니다.</p>
        </header>
        <div class="mg-point-charge__card">
            <p style="margin:0 0 1.25rem;color:#64748b;line-height:1.6;">아직 입금하지 않으셨다면 충전 페이지의 계좌로 입금해 주세요. 입금자명은 신청 시 입력한 이름과 동일해야 합니다.</p>
            <a href="<?php echo htmlspecialchars($charge_url, ENT_QUOTES, 'UTF-8'); ?>" class="mg-point-charge-form__submit">충전 페이지로 돌아가기</a>
            <p class="mg-point-charge__links" style="margin-top:1rem;">
                <a href="<?php echo $point_url; ?>">포인트 내역 보기</a>
            </p>
        </div>
    </div>
</div>
<?php g5_page_end('minimal'); ?>
