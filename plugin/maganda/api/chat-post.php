<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    maganda_json_response(array('ok' => false, 'message' => 'POST only.'), 405);
}

$creator_id = isset($_POST['creator_id']) ? (int) $_POST['creator_id'] : 0;
$message = isset($_POST['message']) ? maganda_filter_message($_POST['message']) : '';

if ($creator_id < 1 || $message === '') {
    maganda_json_response(array('ok' => false, 'message' => '메시지를 입력해 주세요.'), 400);
}

$row = maganda_get_creator_row((string) $creator_id);
if (!$row) {
    maganda_json_response(array('ok' => false, 'message' => '방송회원을 찾을 수 없습니다.'), 404);
}

global $is_member, $member;
$mb_id = ($is_member && isset($member['mb_id'])) ? $member['mb_id'] : '';
$guest = $mb_id !== '' ? '' : 'Guest';

$table = maganda_table('chat');
sql_query(
    " INSERT INTO `{$table}`
        (`mc_id`,`mb_id`,`mch_guest`,`mch_type`,`mch_message`,`mch_datetime`)
      VALUES
        ({$creator_id}, '" . sql_escape_string($mb_id) . "', '" . sql_escape_string($guest) . "', 'chat', '" . sql_escape_string($message) . "', '" . G5_TIME_YMDHIS . "') "
);

maganda_json_response(array(
    'ok' => true,
    'message' => '등록되었습니다.',
));
