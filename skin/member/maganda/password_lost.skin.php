<?php
if (!defined('_GNUBOARD_')) exit;

include_once G5_SKIN_PATH . '/member/maganda/_maganda_init.skin.php';

if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp'])) {
    add_javascript('<script src="' . G5_JS_URL . '/certify.js?v=' . G5_JS_VER . '"></script>', 0);
}

mg_auth_shell_open(
    'Account',
    '아이디/비밀번호 찾기',
    '가입 시 등록한 이메일로 아이디와 임시 비밀번호를 보내드립니다.'
);
mg_auth_nav('find');
?>

<!-- 회원정보 찾기 시작 { -->
<div id="find_info" class="new_win<?php if ($config['cf_cert_use'] != 0 && $config['cf_cert_find'] != 0) { ?> cert<?php } ?>">
    <div class="new_win_con">
        <form name="fpasswordlost" action="<?php echo $action_url ?>" onsubmit="return fpasswordlost_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="cert_no" value="">
        <h3>이메일로 찾기</h3>
        <fieldset id="info_fs">
            <p>
                회원가입 시 등록하신 이메일 주소를 입력해 주세요.<br>
                해당 이메일로 아이디와 비밀번호 정보를 보내드립니다.
            </p>
            <label for="mb_email">E-mail 주소 <span class="required-mark">*</span></label>
            <input type="text" name="mb_email" id="mb_email" required class="required frm_input full_input email" size="30" placeholder="example@email.com">
        </fieldset>
        <?php echo captcha_html(); ?>
        <div class="win_btn">
            <button type="submit" class="btn_submit">인증메일 보내기</button>
        </div>
        </form>
    </div>
    <?php if ($config['cf_cert_use'] != 0 && $config['cf_cert_find'] != 0) { ?>
    <div class="new_win_con find_btn">
        <h3>본인인증으로 찾기</h3>
        <div class="cert_btn">
        <?php if (!empty($config['cf_cert_simple'])) { ?>
            <button type="button" id="win_sa_kakao_cert" class="btn_submit win_sa_cert" data-type="">간편인증</button>
        <?php } if (!empty($config['cf_cert_hp']) || !empty($config['cf_cert_ipin'])) { ?>
            <?php if (!empty($config['cf_cert_hp'])) { ?>
            <button type="button" id="win_hp_cert" class="btn_submit">휴대폰 본인확인</button>
            <?php } if (!empty($config['cf_cert_ipin'])) { ?>
            <button type="button" id="win_ipin_cert" class="btn_submit">아이핀 본인확인</button>
            <?php } ?>
        <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>

<div class="mg-auth-links" style="margin-top:1rem;">
    <a href="<?php echo G5_BBS_URL ?>/login.php">로그인으로 돌아가기</a>
    <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a>
</div>

<script>
$(function() {
    $("#reg_zip_find").css("display", "inline-block");
    var pageTypeParam = "pageType=find";
    <?php if ($config['cf_cert_use'] && $config['cf_cert_simple']) { ?>
    var url = "<?php echo G5_INICERT_URL; ?>/ini_request.php";
    $(".win_sa_cert").click(function() {
        var type = $(this).data("type");
        call_sa(url + "?directAgency=" + type + "&" + pageTypeParam);
    });
    <?php } ?>
    <?php if ($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
    $("#win_ipin_cert").click(function() {
        certify_win_open('kcb-ipin', "<?php echo G5_OKNAME_URL; ?>/ipin1.php?" + pageTypeParam);
        return false;
    });
    <?php } ?>
    <?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
    $("#win_hp_cert").click(function() {
        <?php
        switch ($config['cf_cert_hp']) {
            case 'kcb':
                $cert_url = G5_OKNAME_URL . '/hpcert1.php';
                $cert_type = 'kcb-hp';
                break;
            case 'kcp':
                $cert_url = G5_KCPCERT_URL . '/kcpcert_form.php';
                $cert_type = 'kcp-hp';
                break;
            case 'lg':
                $cert_url = G5_LGXPAY_URL . '/AuthOnlyReq.php';
                $cert_type = 'lg-hp';
                break;
            default:
                echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오"); return false;';
                break;
        }
        if (isset($cert_url, $cert_type)) {
            echo 'certify_win_open("' . $cert_type . '", "' . $cert_url . '?" + pageTypeParam); return false;';
        }
        ?>
    });
    <?php } ?>
});
function fpasswordlost_submit(f) {
    <?php echo chk_captcha_js(); ?>
    return true;
}
</script>
<!-- } 회원정보 찾기 끝 -->

<?php mg_auth_shell_close(); ?>
