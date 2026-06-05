<?php
$sub_menu = '200920';
require_once __DIR__ . '/_common.php';

$g5['title'] = '마간다TV 관리';
include_once G5_ADMIN_PATH . '/admin.head.php';

$table = maganda_table('creator');
$creator_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `{$table}` ");
$app_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `" . maganda_table('application') . "` WHERE `ma_status` = 'pending' ");
$sample_row = sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_slug` = '" . sql_escape_string(MAGANDA_SAMPLE_LIVE_SLUG) . "' ");
$sample_ready = $sample_row
    && maganda_stream_video_id($sample_row) === maganda_youtube_id_from_url(MAGANDA_SAMPLE_LIVE_URL)
    && (int) $sample_row['mc_is_live'] === 1
    && (int) $sample_row['mc_enabled'] === 1;
?>
<div class="local_desc01 local_desc">
    <p>마간다TV 홈페이지의 방송회원·신청·선물 데이터를 관리합니다.</p>
</div>

<?php if (!$sample_ready) { ?>
<div style="margin:0 0 16px;padding:14px 16px;border:1px solid #e9d5ff;border-radius:10px;background:#faf5ff;">
    <strong>샘플 YouTube LIVE가 아직 연결되지 않았습니다.</strong><br>
    <span style="font-size:13px;">방송회원 관리에서 <b>샘플 YouTube LIVE 연결</b> 버튼을 누르면 Jolie(졸리) 회원에 샘플 방송이 설정됩니다.</span>
</div>
<?php } else { ?>
<div style="margin:0 0 16px;padding:12px 14px;border-radius:8px;background:#ecfdf5;border:1px solid #86efac;color:#166534;font-size:13px;">
    샘플 방송 Jolie(졸리) — YouTube LIVE 연결됨. <a href="<?php echo G5_URL; ?>/" target="_blank">홈페이지에서 보기</a>
</div>
<?php } ?>

<ul class="anchor">
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php">방송회원 관리</a></li>
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/applications.php">방송회원 신청</a></li>
    <li><a href="<?php echo G5_PLUGIN_URL; ?>/maganda/install.php">DB 재설치/시드</a></li>
    <li><a href="<?php echo G5_URL; ?>/" target="_blank">홈페이지 보기</a></li>
</ul>

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
            <th scope="row">샘플 LIVE</th>
            <td>
                <?php if ($sample_ready) { ?>
                    연결됨 (<?php echo get_text(MAGANDA_SAMPLE_LIVE_SLUG); ?> / <?php echo get_text(maganda_youtube_id_from_url(MAGANDA_SAMPLE_LIVE_URL)); ?>)
                <?php } else { ?>
                    <span style="color:#dc2626;">미연결</span> —
                    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php">방송회원 관리</a>에서 연결
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">모듈 버전</th>
            <td><?php echo MAGANDA_VERSION; ?></td>
        </tr>
    </tbody>
</table>
<?php
include_once G5_ADMIN_PATH . '/admin.tail.php';
