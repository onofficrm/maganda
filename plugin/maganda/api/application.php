<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    maganda_json_response(array('ok' => false, 'message' => 'POST only.'), 405);
}

$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$youtube = isset($_POST['youtube']) ? trim(strip_tags($_POST['youtube'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

if ($name === '' || $email === '' || $phone === '' || $youtube === '') {
    maganda_json_response(array('ok' => false, 'message' => '필수 항목을 입력해 주세요.'), 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    maganda_json_response(array('ok' => false, 'message' => '이메일 형식이 올바르지 않습니다.'), 400);
}

$table = maganda_table('application');
$application = array(
    'ma_name' => cut_str($name, 64, ''),
    'ma_email' => cut_str($email, 128, ''),
    'ma_phone' => cut_str($phone, 32, ''),
    'ma_youtube' => cut_str($youtube, 512, ''),
    'ma_message' => cut_str($message, 2000, ''),
);

sql_query(
    " INSERT INTO `{$table}`
        (`ma_name`,`ma_email`,`ma_phone`,`ma_youtube`,`ma_message`,`ma_status`,`ma_datetime`)
      VALUES
        ('" . sql_escape_string($application['ma_name']) . "','" . sql_escape_string($application['ma_email']) . "','" . sql_escape_string($application['ma_phone']) . "','" . sql_escape_string($application['ma_youtube']) . "','" . sql_escape_string($application['ma_message']) . "','pending','" . G5_TIME_YMDHIS . "') "
);

maganda_notify_application($application);

maganda_json_response(array(
    'ok' => true,
    'message' => '신청이 접수되었습니다. 검토 후 연락드리겠습니다.',
));
