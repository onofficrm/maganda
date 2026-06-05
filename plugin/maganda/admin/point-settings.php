<?php
$sub_menu = '200920';
require_once __DIR__ . '/_common.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();

    maganda_setting_set('bank_name', trim($_POST['bank_name']));
    maganda_setting_set('bank_account', trim($_POST['bank_account']));
    maganda_setting_set('bank_holder', trim($_POST['bank_holder']));
    maganda_setting_set('bank_guide', trim($_POST['bank_guide']));
    maganda_setting_set('point_min_amount', max(100, (int) $_POST['point_min_amount']));

    goto_url(G5_PLUGIN_URL . '/maganda/admin/point-settings.php?saved=1');
}

$bank = maganda_get_bank_settings();
$g5['title'] = '포인트 충전 계좌 설정';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_ov01 local_ov">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/index.php">마간다TV</a> &gt;
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/point-charges.php">포인트 충전 신청</a> &gt;
    계좌 설정
</div>

<?php if (isset($_GET['saved']) && $_GET['saved'] === '1') { ?>
<div class="local_desc01 local_desc" style="border-color:#86efac;background:#ecfdf5;color:#166534;">
    저장되었습니다. <a href="<?php echo G5_URL; ?>/page/point-charge.php" target="_blank">충전 페이지</a>에서 확인해 주세요.
</div>
<?php } ?>

<form method="post">
<?php echo get_admin_token(); ?>
<div class="tbl_frm01 tbl_wrap">
    <table>
        <caption>계좌입금 충전 계좌</caption>
        <tbody>
            <tr>
                <th scope="row"><label for="bank_name">은행명</label></th>
                <td><input type="text" name="bank_name" id="bank_name" value="<?php echo get_text($bank['bank_name']); ?>" class="frm_input" size="40" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="bank_account">계좌번호</label></th>
                <td><input type="text" name="bank_account" id="bank_account" value="<?php echo get_text($bank['bank_account']); ?>" class="frm_input" size="40" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="bank_holder">예금주</label></th>
                <td><input type="text" name="bank_holder" id="bank_holder" value="<?php echo get_text($bank['bank_holder']); ?>" class="frm_input" size="40" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="point_min_amount">최소 충전 포인트</label></th>
                <td><input type="number" name="point_min_amount" id="point_min_amount" value="<?php echo (int) $bank['point_min_amount']; ?>" class="frm_input" min="100" step="100"> P</td>
            </tr>
            <tr>
                <th scope="row"><label for="bank_guide">안내 문구</label></th>
                <td>
                    <textarea name="bank_guide" id="bank_guide" rows="6" class="frm_input" style="width:100%;max-width:640px;"><?php echo get_text($bank['bank_guide']); ?></textarea>
                    <p class="frm_info">충전 페이지에 표시됩니다. 줄바꿈 가능.</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="저장" class="btn_submit">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/point-charges.php" class="btn_frmline">충전 신청 목록</a>
</div>
</form>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
