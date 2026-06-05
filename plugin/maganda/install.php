<?php
define('G5_IS_ADMIN', true);
require_once dirname(__DIR__, 3) . '/common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.', G5_URL);
}

include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
maganda_install();

alert('마간다TV 모듈 설치/시드가 완료되었습니다.', G5_PLUGIN_URL . '/maganda/admin/index.php');
