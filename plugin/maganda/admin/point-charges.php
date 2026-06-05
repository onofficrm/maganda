<?php
$sub_menu = '200920';
require_once __DIR__ . '/_common.php';

$table = maganda_table('point_charge');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $mp_id = isset($_POST['mp_id']) ? (int) $_POST['mp_id'] : 0;
    $admin_memo = isset($_POST['admin_memo']) ? trim($_POST['admin_memo']) : '';

    if ($mp_id > 0 && $action === 'approve') {
        $result = maganda_approve_point_charge($mp_id, $member['mb_id']);
        alert($result['message'], G5_PLUGIN_URL . '/maganda/admin/point-charges.php');
    }

    if ($mp_id > 0 && $action === 'reject') {
        $result = maganda_reject_point_charge($mp_id, $member['mb_id'], $admin_memo);
        alert($result['message'], G5_PLUGIN_URL . '/maganda/admin/point-charges.php');
    }
}

$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'pending';
$where = '1=1';
if ($status_filter !== '' && $status_filter !== 'all') {
    $where = "`mp_status` = '" . sql_escape_string($status_filter) . "'";
}

$list = array();
$result = sql_query(" SELECT * FROM `{$table}` WHERE {$where} ORDER BY `mp_id` DESC LIMIT 100 ");
while ($row = sql_fetch_array($result)) {
    $mb = get_member($row['mb_id'], 'mb_nick, mb_name');
    $row['mb_nick'] = isset($mb['mb_nick']) ? $mb['mb_nick'] : '';
    $list[] = $row;
}

$pending_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `{$table}` WHERE `mp_status` = 'pending' ");
$bank = maganda_get_bank_settings();

$g5['title'] = '포인트 충전 신청';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_ov01 local_ov">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/index.php">마간다TV</a> &gt; 포인트 충전 신청
</div>

<div class="local_desc01 local_desc">
    <strong>계좌:</strong> <?php echo get_text($bank['bank_name']); ?> <?php echo get_text($bank['bank_account']); ?> (<?php echo get_text($bank['bank_holder']); ?>)
    — <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/point-settings.php">계좌 수정</a>
</div>

<ul class="anchor" style="margin-bottom:12px;">
    <li><a href="?status=pending" <?php echo $status_filter === 'pending' ? 'class="ov_listall"' : ''; ?>>대기 (<?php echo number_format((int) $pending_count['cnt']); ?>)</a></li>
    <li><a href="?status=approved" <?php echo $status_filter === 'approved' ? 'class="ov_listall"' : ''; ?>>완료</a></li>
    <li><a href="?status=rejected" <?php echo $status_filter === 'rejected' ? 'class="ov_listall"' : ''; ?>>거절</a></li>
    <li><a href="?status=all" <?php echo $status_filter === 'all' ? 'class="ov_listall"' : ''; ?>>전체</a></li>
</ul>

<div class="tbl_head01 tbl_wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>회원</th>
                <th>포인트</th>
                <th>입금자명</th>
                <th>메모</th>
                <th>상태</th>
                <th>신청일</th>
                <th>처리</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($list)) { ?>
            <tr><td colspan="8">내역이 없습니다.</td></tr>
        <?php } else { foreach ($list as $row) { ?>
            <tr>
                <td><?php echo (int) $row['mp_id']; ?></td>
                <td>
                    <?php echo get_text($row['mb_id']); ?><br>
                    <span style="color:#666;font-size:12px;"><?php echo get_text($row['mb_nick']); ?></span>
                </td>
                <td><strong><?php echo number_format((int) $row['mp_amount']); ?>P</strong></td>
                <td><?php echo get_text($row['mp_depositor']); ?></td>
                <td><?php echo get_text($row['mp_memo']); ?></td>
                <td><?php echo get_text(maganda_point_charge_status_label($row['mp_status'])); ?></td>
                <td><?php echo get_text($row['mp_datetime']); ?></td>
                <td>
                    <?php if ($row['mp_status'] === 'pending') { ?>
                    <form method="post" style="display:inline;">
                        <?php echo get_admin_token(); ?>
                        <input type="hidden" name="mp_id" value="<?php echo (int) $row['mp_id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn_frmline" onclick="return confirm('입금을 확인하고 <?php echo number_format((int) $row['mp_amount']); ?>P를 충전하시겠습니까?');">입금 확인</button>
                    </form>
                    <form method="post" style="display:inline;margin-top:4px;">
                        <?php echo get_admin_token(); ?>
                        <input type="hidden" name="mp_id" value="<?php echo (int) $row['mp_id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <input type="text" name="admin_memo" placeholder="거절 사유" class="frm_input" size="12">
                        <button type="submit" class="btn_frmline" onclick="return confirm('이 신청을 거절하시겠습니까?');">거절</button>
                    </form>
                    <?php } else { ?>
                        <?php echo $row['mp_confirmed_datetime'] !== '0000-00-00 00:00:00' ? get_text($row['mp_confirmed_datetime']) : '-'; ?>
                        <?php if ($row['mp_admin_memo'] !== '') { ?><br><span style="font-size:12px;color:#666;"><?php echo get_text($row['mp_admin_memo']); ?></span><?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
