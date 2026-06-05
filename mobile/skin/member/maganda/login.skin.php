<?php
if (!defined('_GNUBOARD_')) exit;

include_once G5_SKIN_PATH . '/member/maganda/_maganda_init.skin.php';

mg_auth_shell_open(
    'Account',
    '로그인',
    '마간다TV 계정으로 로그인하고 라이브·후원 기능을 이용해 보세요.'
);
mg_auth_nav('login');
?>

<!-- 로그인 시작 { -->
<div id="mb_login" class="mbskin">
    <div class="mbskin_box">
        <div class="mb_log_cate">
            <h2><span class="sound_only">회원</span>로그인</h2>
            <a href="<?php echo G5_BBS_URL ?>/register.php" class="join">회원가입</a>
        </div>
        <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
        <input type="hidden" name="url" value="<?php echo $login_url ?>">

        <fieldset id="login_fs">
            <legend class="sound_only">회원로그인</legend>
            <label for="login_id">아이디 <span class="required-mark">*</span></label>
            <input type="text" name="mb_id" id="login_id" required class="frm_input required" size="20" maxLength="20" placeholder="아이디">
            <label for="login_pw">비밀번호 <span class="required-mark">*</span></label>
            <input type="password" name="mb_password" id="login_pw" required class="frm_input required" size="20" maxLength="20" placeholder="비밀번호">
            <button type="submit" class="btn_submit">로그인</button>

            <div id="login_info">
                <div class="login_if_auto chk_box">
                    <input type="checkbox" name="auto_login" id="login_auto_login" class="selec_chk">
                    <label for="login_auto_login"><span></span> 자동로그인</label>
                </div>
                <div class="login_if_lpl mg-auth-links">
                    <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디/비밀번호 찾기</a>
                </div>
            </div>
        </fieldset>
        </form>
        <?php @include_once(get_social_skin_path().'/social_login.skin.php'); ?>
    </div>

    <?php if (isset($default['de_level_sell']) && $default['de_level_sell'] == 1) { ?>
    <?php if (preg_match("/orderform.php/", $url)) { ?>
    <section id="mb_login_notmb">
        <h2>비회원 구매</h2>
        <p>비회원으로 주문하시는 경우 포인트는 지급하지 않습니다.</p>
        <div id="guest_privacy">
            <?php echo conv_content($default['de_guest_privacy'], $config['cf_editor']); ?>
        </div>
        <div class="chk_box">
            <input type="checkbox" id="agree" value="1" class="selec_chk">
            <label for="agree"><span></span> 개인정보수집에 대한 내용을 읽었으며 이에 동의합니다.</label>
        </div>
        <div class="btn_confirm">
            <a href="javascript:guest_submit(document.flogin);" class="btn_submit">비회원으로 구매하기</a>
        </div>
        <script>
        function guest_submit(f) {
            if (document.getElementById('agree') && !document.getElementById('agree').checked) {
                alert("개인정보수집에 대한 내용을 읽고 이에 동의하셔야 합니다.");
                return;
            }
            f.url.value = "<?php echo $url; ?>";
            f.action = "<?php echo $url; ?>";
            f.submit();
        }
        </script>
    </section>
    <?php } else if (preg_match("/orderinquiry.php$/", $url)) { ?>
    <div id="mb_login_od_wr">
        <h2>비회원 주문조회</h2>
        <fieldset id="mb_login_od">
            <legend>비회원 주문조회</legend>
            <form name="forderinquiry" method="post" action="<?php echo urldecode($url); ?>" autocomplete="off">
            <label for="od_id">주문서번호 <span class="required-mark">*</span></label>
            <input type="text" name="od_id" value="<?php echo $od_id; ?>" id="od_id" required class="frm_input required" size="20" placeholder="주문서번호">
            <label for="od_pwd">비밀번호 <span class="required-mark">*</span></label>
            <input type="password" name="od_pwd" size="20" id="od_pwd" required class="frm_input required" placeholder="비밀번호">
            <button type="submit" class="btn_submit">확인</button>
            </form>
        </fieldset>
    </div>
    <?php } ?>
    <?php } ?>
</div>

<script>
jQuery(function($){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});
function flogin_submit(f) {
    if ($(document.body).triggerHandler('login_sumit', [f, 'flogin']) !== false) {
        return true;
    }
    return false;
}
</script>
<!-- } 로그인 끝 -->

<?php mg_auth_shell_close(); ?>
