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
$tiktok = isset($_POST['tiktok']) ? trim(strip_tags($_POST['tiktok'])) : '';
$instagram = isset($_POST['instagram']) ? trim(strip_tags($_POST['instagram'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

if ($name === '' || $email === '' || $phone === '') {
    maganda_json_response(array('ok' => false, 'message' => 'Please fill in all required fields.'), 400);
}

if ($youtube === '' && $tiktok === '' && $instagram === '') {
    maganda_json_response(array('ok' => false, 'message' => 'Please provide at least one live platform URL (YouTube, TikTok, or Instagram).'), 400);
}

if ($youtube !== '' && maganda_detect_stream_platform($youtube) !== 'youtube') {
    maganda_json_response(array('ok' => false, 'message' => 'Please enter a valid YouTube URL.'), 400);
}

if ($tiktok !== '' && maganda_tiktok_username_from_url($tiktok) === '') {
    maganda_json_response(array('ok' => false, 'message' => 'Please enter a valid TikTok profile or live URL.'), 400);
}

if ($instagram !== '' && maganda_instagram_username_from_url($instagram) === '') {
    maganda_json_response(array('ok' => false, 'message' => 'Please enter a valid Instagram profile URL.'), 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    maganda_json_response(array('ok' => false, 'message' => 'Please enter a valid email address.'), 400);
}

$table = maganda_table('application');
$application = array(
    'ma_name' => cut_str($name, 64, ''),
    'ma_email' => cut_str($email, 128, ''),
    'ma_phone' => cut_str($phone, 32, ''),
    'ma_youtube' => cut_str($youtube, 512, ''),
    'ma_tiktok' => cut_str($tiktok, 512, ''),
    'ma_instagram' => cut_str($instagram, 512, ''),
    'ma_message' => cut_str($message, 2000, ''),
);

sql_query(
    " INSERT INTO `{$table}`
        (`ma_name`,`ma_email`,`ma_phone`,`ma_youtube`,`ma_tiktok`,`ma_instagram`,`ma_message`,`ma_status`,`ma_datetime`)
      VALUES
        ('" . sql_escape_string($application['ma_name']) . "','" . sql_escape_string($application['ma_email']) . "','" . sql_escape_string($application['ma_phone']) . "','" . sql_escape_string($application['ma_youtube']) . "','" . sql_escape_string($application['ma_tiktok']) . "','" . sql_escape_string($application['ma_instagram']) . "','" . sql_escape_string($application['ma_message']) . "','pending','" . G5_TIME_YMDHIS . "') "
);

maganda_notify_application($application);

maganda_json_response(array(
    'ok' => true,
    'message' => 'Your application has been received. We will contact you after review.',
));
