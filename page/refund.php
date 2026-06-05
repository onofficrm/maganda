<?php
include_once(dirname(__FILE__).'/_init.php');

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$company = function_exists('g5site_cfg') ? g5site_cfg('company_name', $site_name) : $site_name;
$email = function_exists('g5site_cfg') ? g5site_cfg('email', 'help@example.com') : 'help@example.com';

g5_page_start('환불정책');
?>
<div class="page-template page-refund">
    <header class="page-hero reveal">
        <div class="page-inner">
            <p class="page-eyebrow">Refund</p>
            <h1 class="page-title">환불정책</h1>
            <p class="page-desc"><?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?> 포인트 충전 및 환불 기준입니다.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner page-content">
            <h2>1. 포인트 충전</h2>
            <p>충전된 포인트는 서비스 내 선물 후원 등에 사용할 수 있습니다.</p>
            <h2>2. 환불 가능 조건</h2>
            <ul class="page-list">
                <li>충전 후 사용하지 않은 포인트에 한해 환불을 요청할 수 있습니다.</li>
                <li>이미 선물·후원에 사용된 포인트는 환불되지 않습니다.</li>
                <li>부정 사용·약관 위반이 확인된 경우 환불이 제한될 수 있습니다.</li>
            </ul>
            <h2>3. 환불 문의</h2>
            <p>환불 요청은 <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?> 또는 고객센터를 통해 접수해 주세요.</p>
            <h2>4. 처리 기간</h2>
            <p>환불 승인 후 영업일 기준 3~7일 이내 결제 수단으로 환불 처리됩니다.</p>
        </div>
    </section>
</div>
<?php g5_page_end(); ?>
