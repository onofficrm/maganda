<?php
define('G5_IS_ADMIN', true);
require_once dirname(__DIR__, 2) . '/common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.', G5_URL);
}

include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
maganda_install();
$sample_id = maganda_apply_sample_live_creator();

alert('마간다TV 모듈 설치/시드가 완료되었습니다.\\n샘플 방송회원(Jolie)에 YouTube LIVE가 연결되었습니다.', G5_PLUGIN_URL . '/maganda/admin/creators.php?id=' . $sample_id);
