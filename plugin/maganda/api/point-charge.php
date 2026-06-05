<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();
maganda_require_member_api();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    maganda_json_response(array('ok' => false, 'message' => 'POST only.'), 405);
}

global $member;

$amount = isset($_POST['amount']) ? (int) preg_replace('/[^0-9]/', '', (string) $_POST['amount']) : 0;
$depositor = isset($_POST['depositor']) ? trim($_POST['depositor']) : '';
$memo = isset($_POST['memo']) ? trim($_POST['memo']) : '';

$result = maganda_create_point_charge($member['mb_id'], $amount, $depositor, $memo);
$code = !empty($result['ok']) ? 200 : 400;

maganda_json_response($result, $code);
