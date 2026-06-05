<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    maganda_json_response(array('ok' => false, 'message' => 'POST only.'), 405);
}

maganda_require_member_api();

global $member;

$creator_id = isset($_POST['creator_id']) ? (int) $_POST['creator_id'] : 0;
if ($creator_id < 1) {
    maganda_json_response(array('ok' => false, 'message' => 'creator_id가 필요합니다.'), 400);
}

$row = maganda_get_creator_row((string) $creator_id);
if (!$row) {
    maganda_json_response(array('ok' => false, 'message' => '방송회원을 찾을 수 없습니다.'), 404);
}

$table = maganda_table('follow');
$existing = sql_fetch(" SELECT mf_id FROM `{$table}` WHERE `mb_id` = '" . sql_escape_string($member['mb_id']) . "' AND `mc_id` = {$creator_id} ");

if ($existing) {
    sql_query(" DELETE FROM `{$table}` WHERE `mf_id` = " . (int) $existing['mf_id']);
    sql_query(" UPDATE `" . maganda_table('creator') . "` SET `mc_followers` = GREATEST(0, `mc_followers` - 1) WHERE `mc_id` = {$creator_id} ");
    maganda_json_response(array('ok' => true, 'following' => false));
}

sql_query(
    " INSERT INTO `{$table}` (`mb_id`,`mc_id`,`mf_datetime`) VALUES ('" . sql_escape_string($member['mb_id']) . "', {$creator_id}, '" . G5_TIME_YMDHIS . "') "
);
sql_query(" UPDATE `" . maganda_table('creator') . "` SET `mc_followers` = `mc_followers` + 1 WHERE `mc_id` = {$creator_id} ");

maganda_json_response(array('ok' => true, 'following' => true));
