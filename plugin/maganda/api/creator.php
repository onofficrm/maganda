<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$key = $slug !== '' ? $slug : $id;

$row = maganda_get_creator_row($key);
if (!$row) {
    maganda_json_response(array('ok' => false, 'message' => '방송회원을 찾을 수 없습니다.'), 404);
}

$creator = maganda_creator_to_room($row);
$gifts = maganda_get_gifts();
$chat = maganda_get_chat_messages((int) $row['mc_id'], 50);
$following = false;

global $is_member, $member;
if ($is_member && $creator) {
    $following = maganda_is_following($member['mb_id'], (int) $creator['id']);
}

maganda_json_response(array(
    'ok' => true,
    'creator' => $creator,
    'gifts' => $gifts,
    'chat' => $chat,
    'following' => $following,
));
