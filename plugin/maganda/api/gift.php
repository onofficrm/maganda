<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    maganda_json_response(array('ok' => false, 'message' => 'POST only.'), 405);
}

maganda_require_member_api();

global $member;

$creator_id = isset($_POST['creator_id']) ? (int) $_POST['creator_id'] : 0;
$gift_key = isset($_POST['gift_key']) ? trim($_POST['gift_key']) : '';
$message = isset($_POST['message']) ? maganda_filter_message($_POST['message']) : '';

if ($creator_id < 1 || $gift_key === '') {
    maganda_json_response(array('ok' => false, 'message' => '선물 정보가 올바르지 않습니다.'), 400);
}

$creator = maganda_get_creator_row((string) $creator_id);
if (!$creator) {
    maganda_json_response(array('ok' => false, 'message' => '방송회원을 찾을 수 없습니다.'), 404);
}

$gift_table = maganda_table('gift');
$gift = sql_fetch(" SELECT * FROM `{$gift_table}` WHERE `mg_key` = '" . sql_escape_string($gift_key) . "' AND `mg_enabled` = 1 ");
if (!$gift) {
    maganda_json_response(array('ok' => false, 'message' => '선물을 찾을 수 없습니다.'), 404);
}

$points = (int) $gift['mg_points'];
if ((int) $member['mb_point'] < $points) {
    maganda_json_response(array(
        'ok' => false,
        'error' => 'insufficient_points',
        'message' => '포인트가 부족합니다.',
    ), 400);
}

insert_point($member['mb_id'], $points * -1, $creator['mc_name'] . ' 선물 - ' . $gift['mg_name'], '@gift', $member['mb_id'], $member['mb_id'] . '-' . uniqid('', true));

$donation_table = maganda_table('donation');
sql_query(
    " INSERT INTO `{$donation_table}`
        (`mc_id`,`mb_id`,`mg_id`,`md_points`,`md_message`,`md_datetime`)
      VALUES
        ({$creator_id}, '" . sql_escape_string($member['mb_id']) . "', " . (int) $gift['mg_id'] . ", {$points}, '" . sql_escape_string($message) . "', '" . G5_TIME_YMDHIS . "') "
);

$creator_table = maganda_table('creator');
sql_query(" UPDATE `{$creator_table}` SET `mc_total_points` = `mc_total_points` + {$points} WHERE `mc_id` = {$creator_id} ");

$chat_table = maganda_table('chat');
sql_query(
    " INSERT INTO `{$chat_table}`
        (`mc_id`,`mb_id`,`mch_guest`,`mch_type`,`mch_message`,`mch_gift_name`,`mch_gift_points`,`mch_datetime`)
      VALUES
        ({$creator_id}, '" . sql_escape_string($member['mb_id']) . "', '', 'gift', '" . sql_escape_string($message) . "', '" . sql_escape_string($gift['mg_name']) . "', {$points}, '" . G5_TIME_YMDHIS . "') "
);

$member = get_member($member['mb_id']);

maganda_json_response(array(
    'ok' => true,
    'message' => '선물을 보냈습니다.',
    'points' => (int) $member['mb_point'],
));
