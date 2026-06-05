<?php
define('G5_IS_ADMIN', true);
require_once dirname(__DIR__, 3) . '/common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.', G5_URL);
}

include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
maganda_bootstrap();

$g5['title'] = '마간다TV 관리';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_desc01 local_desc">
    <p>마간다TV 홈페이지의 방송회원·신청·선물 데이터를 관리합니다.</p>
</div>

<ul class="anchor">
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php">방송회원 관리</a></li>
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/applications.php">방송회원 신청</a></li>
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/install.php">DB 재설치/시드</a></li>
    <li><a href="<?php echo G5_URL; ?>/" target="_blank">홈페이지 보기</a></li>
</ul>

<?php
$table = maganda_table('creator');
$creator_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `{$table}` ");
$app_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `" . maganda_table('application') . "` WHERE `ma_status` = 'pending' ");
?>
<table class="tbl_frm01">
    <tbody>
        <tr>
            <th scope="row">등록 방송회원</th>
            <td><?php echo number_format((int) $creator_count['cnt']); ?>명</td>
        </tr>
        <tr>
            <th scope="row">대기 중 신청</th>
            <td><?php echo number_format((int) $app_count['cnt']); ?>건</td>
        </tr>
        <tr>
            <th scope="row">모듈 버전</th>
            <td><?php echo MAGANDA_VERSION; ?></td>
        </tr>
    </tbody>
</table>
<?php
include_once G5_ADMIN_PATH . '/admin.tail.php';
