<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === '_common.php') {
    exit;
}

define('G5_IS_ADMIN', true);
require_once __DIR__ . '/../../../common.php';
require_once G5_ADMIN_PATH . '/admin.lib.php';

if (!isset($sub_menu)) {
    $sub_menu = '200920';
}

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.', G5_URL);
}

include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
maganda_bootstrap();
