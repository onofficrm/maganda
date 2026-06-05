<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

$creator_id = isset($_GET['creator_id']) ? (int) $_GET['creator_id'] : 0;
$since_id = isset($_GET['since_id']) ? (int) $_GET['since_id'] : 0;

if ($creator_id < 1) {
    maganda_json_response(array('ok' => false, 'message' => 'creator_id가 필요합니다.'), 400);
}

$table = maganda_table('chat');
$sql = " SELECT * FROM `{$table}` WHERE `mc_id` = {$creator_id} AND `mch_deleted` = 0 ";
if ($since_id > 0) {
    $sql .= " AND `mch_id` > {$since_id} ";
}
$sql .= ' ORDER BY `mch_id` ASC LIMIT 100 ';

$result = sql_query($sql, false);
$rows = array();
if ($result) {
    while ($row = sql_fetch_array($result)) {
        $rows[] = $row;
    }
}

$messages = array();
foreach ($rows as $row) {
    $user = $row['mch_guest'] !== '' ? $row['mch_guest'] : $row['mb_id'];
    if ($row['mb_id'] !== '' && function_exists('get_member')) {
        $member = get_member($row['mb_id']);
        if ($member && $member['mb_nick'] !== '') {
            $user = $member['mb_nick'];
        }
    }

    $time = $row['mch_datetime'] !== '0000-00-00 00:00:00'
        ? substr($row['mch_datetime'], 11, 5)
        : '10:00';

    if ($row['mch_type'] === 'gift') {
        $messages[] = array(
            'id' => (int) $row['mch_id'],
            'type' => 'gift',
            'user' => $user,
            'gift' => $row['mch_gift_name'],
            'points' => (int) $row['mch_gift_points'],
            'text' => $row['mch_message'],
            'time' => $time,
        );
        continue;
    }

    $messages[] = array(
        'id' => (int) $row['mch_id'],
        'type' => 'chat',
        'user' => $user,
        'text' => $row['mch_message'],
        'time' => $time,
    );
}

maganda_json_response(array(
    'ok' => true,
    'messages' => $messages,
));
