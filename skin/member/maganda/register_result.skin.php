<?php
if (!defined('_GNUBOARD_')) exit;

include_once G5_SKIN_PATH . '/member/maganda/_maganda_init.skin.php';

mg_auth_shell_open(
    'Welcome',
    '회원가입 완료',
    '마간다TV 가입을 환영합니다.'
);
?>

<!-- 회원가입결과 시작 { -->
<div id="reg_result" class="register">
    <p class="reg_result_p" style="text-align:center;line-height:1.8;margin-bottom:1.5rem;">
        <i class="fa fa-gift" aria-hidden="true" style="font-size:2rem;color:#7c3aed;"></i><br>
        <strong><?php echo get_text($mb['mb_name']); ?></strong>님, 회원가입을 진심으로 축하합니다.
    </p>

    <?php if (is_use_email_certify()) { ?>
    <div class="mg-auth-note">
        <i class="fa fa-envelope" aria-hidden="true"></i>
        <span>
            가입 이메일(<strong><?php echo $mb['mb_email'] ?></strong>)로 인증메일이 발송되었습니다.
            메일 인증 후 모든 기능을 이용할 수 있습니다.
        </span>
    </div>
    <?php } ?>

    <p class="result_txt" style="font-size:0.875rem;line-height:1.65;color:var(--color-muted,#64748b);">
        비밀번호는 암호화되어 안전하게 저장됩니다. 분실 시 이메일 찾기를 이용해 주세요.
    </p>
</div>
<!-- } 회원가입결과 끝 -->
<div class="btn_confirm_reg btn_confirm">
    <a href="<?php echo G5_BBS_URL ?>/login.php" class="btn_submit" style="text-align:center;text-decoration:none;">로그인</a>
    <a href="<?php echo G5_URL ?>/" class="btn_close">메인으로</a>
</div>

<?php mg_auth_shell_close(); ?>
