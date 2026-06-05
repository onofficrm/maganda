<?php
include_once(dirname(__FILE__).'/_init.php');

if (!function_exists('g5site_cfg')) {
    if (is_file(G5_PATH . '/_site.config.php')) {
        include_once G5_PATH . '/_site.config.php';
    }
}

$site_name = function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
$company = function_exists('g5site_cfg') ? g5site_cfg('company_name', $site_name) : $site_name;

g5_page_start('이용약관');
?>
<div class="page-template page-terms">
    <header class="page-hero reveal">
        <div class="page-inner">
            <p class="page-eyebrow">Terms</p>
            <h1 class="page-title">이용약관</h1>
            <p class="page-desc"><?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?> 서비스 이용에 관한 기본 약관입니다.</p>
        </div>
    </header>
    <section class="page-section reveal">
        <div class="page-inner page-content">
            <h2>제1조 (목적)</h2>
            <p>본 약관은 <?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>(이하 &quot;회사&quot;)가 제공하는 <?php echo htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8'); ?> 서비스의 이용 조건 및 절차를 정함을 목적으로 합니다.</p>
            <h2>제2조 (서비스 내용)</h2>
            <p>회사는 필리핀 크리에이터의 라이브 시청, 채팅, 포인트 선물 후원 등의 서비스를 제공합니다.</p>
            <h2>제3조 (회원의 의무)</h2>
            <p>회원은 타인의 권리를 침해하거나 불법·음란·광고성 메시지를 게시해서는 안 됩니다. 위반 시 서비스 이용이 제한될 수 있습니다.</p>
            <h2>제4조 (포인트)</h2>
            <p>포인트는 서비스 내 선물 후원 등에 사용되며, 충전·환불 조건은 별도 환불정책을 따릅니다.</p>
            <h2>제5조 (면책)</h2>
            <p>회사는 크리에이터 개인 방송 콘텐츠에 대해 법령상 책임 범위 내에서만 책임을 집니다.</p>
        </div>
    </section>
</div>
<?php g5_page_end(); ?>
