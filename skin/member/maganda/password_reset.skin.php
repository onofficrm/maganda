<?php
if (!defined('_GNUBOARD_')) exit;

include_once G5_SKIN_PATH . '/member/maganda/_maganda_init.skin.php';

mg_auth_shell_open(
    'Security',
    '비밀번호 재설정',
    '새로운 비밀번호를 입력해 주세요.'
);
?>

<!-- 비밀번호 재설정 시작 { -->
<div id="pw_reset" class="new_win">
    <div class="new_win_con">
        <form name="fpasswordreset" action="<?php echo $action_url; ?>" onsubmit="return fpasswordreset_submit(this);" method="post" autocomplete="off">
            <fieldset id="info_fs">
                <p>새로운 비밀번호를 입력해주세요.</p>
                <p style="margin-bottom:1rem;"><b>회원 아이디 : <?php echo get_text($_POST['mb_id']); ?></b></p>
                <label for="mb_pw">새 비밀번호 <span class="required-mark">*</span></label>
                <input type="password" name="mb_password" id="mb_pw" required class="required frm_input full_input" size="30" placeholder="새 비밀번호">
                <label for="mb_pw2">새 비밀번호 확인 <span class="required-mark">*</span></label>
                <input type="password" name="mb_password_re" id="mb_pw2" required class="required frm_input full_input" size="30" placeholder="새 비밀번호 확인">
            </fieldset>
            <div class="win_btn">
                <button type="submit" class="btn_submit">변경하기</button>
            </div>
        </form>
    </div>
</div>

<script>
function fpasswordreset_submit(f) {
    if ($("#mb_pw").val() !== $("#mb_pw2").val()) {
        alert("새 비밀번호와 비밀번호 확인이 일치하지 않습니다.");
        return false;
    }
    alert("비밀번호가 변경되었습니다. 다시 로그인해 주세요.");
    return true;
}
</script>
<!-- } 비밀번호 재설정 끝 -->

<?php mg_auth_shell_close(); ?>
