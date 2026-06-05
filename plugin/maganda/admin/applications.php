<?php
define('G5_IS_ADMIN', true);
require_once dirname(__DIR__, 3) . '/common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.', G5_URL);
}

include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
maganda_bootstrap();

$table = maganda_table('application');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();
    $ma_id = isset($_POST['ma_id']) ? (int) $_POST['ma_id'] : 0;
    $status = isset($_POST['ma_status']) ? trim($_POST['ma_status']) : 'pending';
    if ($ma_id > 0) {
        sql_query(" UPDATE `{$table}` SET `ma_status` = '" . sql_escape_string($status) . "' WHERE `ma_id` = {$ma_id} ");
    }
    goto_url(G5_PLUGIN_URL . '/maganda/admin/applications.php');
}

$list = array();
$result = sql_query(" SELECT * FROM `{$table}` ORDER BY `ma_id` DESC LIMIT 100 ");
while ($row = sql_fetch_array($result)) {
    $list[] = $row;
}

$g5['title'] = '방송회원 신청';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_ov01 local_ov">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/index.php">마간다TV</a> &gt; 신청 목록
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>이름</th>
                <th>이메일</th>
                <th>연락처</th>
                <th>YouTube</th>
                <th>상태</th>
                <th>일시</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($list)) { ?>
            <tr><td colspan="8">신청 내역이 없습니다.</td></tr>
        <?php } else { foreach ($list as $row) { ?>
            <tr>
                <td><?php echo (int) $row['ma_id']; ?></td>
                <td><?php echo get_text($row['ma_name']); ?></td>
                <td><?php echo get_text($row['ma_email']); ?></td>
                <td><?php echo get_text($row['ma_phone']); ?></td>
                <td><a href="<?php echo get_text($row['ma_youtube']); ?>" target="_blank">링크</a></td>
                <td><?php echo get_text($row['ma_status']); ?></td>
                <td><?php echo get_text($row['ma_datetime']); ?></td>
                <td>
                    <form method="post" style="display:inline">
                        <?php echo get_admin_token(); ?>
                        <input type="hidden" name="ma_id" value="<?php echo (int) $row['ma_id']; ?>">
                        <select name="ma_status">
                            <option value="pending" <?php echo $row['ma_status'] === 'pending' ? 'selected' : ''; ?>>pending</option>
                            <option value="review" <?php echo $row['ma_status'] === 'review' ? 'selected' : ''; ?>>review</option>
                            <option value="approved" <?php echo $row['ma_status'] === 'approved' ? 'selected' : ''; ?>>approved</option>
                            <option value="rejected" <?php echo $row['ma_status'] === 'rejected' ? 'selected' : ''; ?>>rejected</option>
                        </select>
                        <input type="submit" value="변경" class="btn_frmline">
                    </form>
                </td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:left;padding:8px 12px;background:#fafafa;"><?php echo nl2br(get_text($row['ma_message'])); ?></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
