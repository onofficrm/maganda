<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

define('MAGANDA_VERSION', '1.0.0');

function maganda_table($name)
{
    global $g5;

    $key = 'maganda_' . $name . '_table';
    if (isset($g5[$key])) {
        return $g5[$key];
    }

    return G5_TABLE_PREFIX . 'maganda_' . $name;
}

function maganda_is_installed()
{
    $table = maganda_table('creator');
    $row = sql_fetch(" SHOW TABLES LIKE '" . sql_escape_string($table) . "' ", false);

    return $row ? true : false;
}

function maganda_install()
{
    if (maganda_is_installed()) {
        return true;
    }

    $charset = (defined('G5_DB_CHARSET') && G5_DB_CHARSET !== '') ? G5_DB_CHARSET : 'utf8mb4';

    $queries = array(
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('creator') . "` (
            `mc_id` int unsigned NOT NULL AUTO_INCREMENT,
            `mc_slug` varchar(64) NOT NULL DEFAULT '',
            `mc_name` varchar(120) NOT NULL DEFAULT '',
            `mc_title` varchar(255) NOT NULL DEFAULT '',
            `mc_avatar` varchar(255) NOT NULL DEFAULT '',
            `mc_cover` varchar(255) NOT NULL DEFAULT '',
            `mc_category` varchar(64) NOT NULL DEFAULT '',
            `mc_intro` varchar(255) NOT NULL DEFAULT '',
            `mc_stream_url` varchar(512) NOT NULL DEFAULT '',
            `mc_youtube_id` varchar(32) NOT NULL DEFAULT '',
            `mc_is_live` tinyint NOT NULL DEFAULT '0',
            `mc_viewers` int unsigned NOT NULL DEFAULT '0',
            `mc_followers` int unsigned NOT NULL DEFAULT '0',
            `mc_total_points` int unsigned NOT NULL DEFAULT '0',
            `mc_sort` int NOT NULL DEFAULT '0',
            `mc_enabled` tinyint NOT NULL DEFAULT '1',
            `mc_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`mc_id`),
            UNIQUE KEY `mc_slug` (`mc_slug`),
            KEY `mc_enabled_sort` (`mc_enabled`,`mc_sort`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('gift') . "` (
            `mg_id` int unsigned NOT NULL AUTO_INCREMENT,
            `mg_key` varchar(32) NOT NULL DEFAULT '',
            `mg_name` varchar(64) NOT NULL DEFAULT '',
            `mg_points` int unsigned NOT NULL DEFAULT '0',
            `mg_icon` varchar(32) NOT NULL DEFAULT '',
            `mg_color` varchar(32) NOT NULL DEFAULT '',
            `mg_bg` varchar(32) NOT NULL DEFAULT '',
            `mg_sort` int NOT NULL DEFAULT '0',
            `mg_enabled` tinyint NOT NULL DEFAULT '1',
            PRIMARY KEY (`mg_id`),
            UNIQUE KEY `mg_key` (`mg_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('donation') . "` (
            `md_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `mc_id` int unsigned NOT NULL DEFAULT '0',
            `mb_id` varchar(20) NOT NULL DEFAULT '',
            `mg_id` int unsigned NOT NULL DEFAULT '0',
            `md_points` int unsigned NOT NULL DEFAULT '0',
            `md_message` varchar(255) NOT NULL DEFAULT '',
            `md_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`md_id`),
            KEY `mc_id` (`mc_id`),
            KEY `md_datetime` (`md_datetime`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('chat') . "` (
            `mch_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `mc_id` int unsigned NOT NULL DEFAULT '0',
            `mb_id` varchar(20) NOT NULL DEFAULT '',
            `mch_guest` varchar(64) NOT NULL DEFAULT '',
            `mch_type` varchar(16) NOT NULL DEFAULT 'chat',
            `mch_message` text NOT NULL,
            `mch_gift_name` varchar(64) NOT NULL DEFAULT '',
            `mch_gift_points` int unsigned NOT NULL DEFAULT '0',
            `mch_deleted` tinyint NOT NULL DEFAULT '0',
            `mch_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`mch_id`),
            KEY `mc_id_datetime` (`mc_id`,`mch_datetime`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('follow') . "` (
            `mf_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `mb_id` varchar(20) NOT NULL DEFAULT '',
            `mc_id` int unsigned NOT NULL DEFAULT '0',
            `mf_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`mf_id`),
            UNIQUE KEY `mb_mc` (`mb_id`,`mc_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
        "CREATE TABLE IF NOT EXISTS `" . maganda_table('application') . "` (
            `ma_id` int unsigned NOT NULL AUTO_INCREMENT,
            `ma_name` varchar(64) NOT NULL DEFAULT '',
            `ma_email` varchar(128) NOT NULL DEFAULT '',
            `ma_phone` varchar(32) NOT NULL DEFAULT '',
            `ma_youtube` varchar(512) NOT NULL DEFAULT '',
            `ma_message` text NOT NULL,
            `ma_status` varchar(16) NOT NULL DEFAULT 'pending',
            `ma_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`ma_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}",
    );

    foreach ($queries as $sql) {
        sql_query($sql, false);
    }

    maganda_seed_defaults();

    return maganda_is_installed();
}

function maganda_bootstrap()
{
    if (!maganda_is_installed()) {
        maganda_install();
    }
}

function maganda_seed_defaults()
{
    $creator_table = maganda_table('creator');
    $gift_table = maganda_table('gift');
    $now = G5_TIME_YMDHIS;

    $count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `{$creator_table}` ");
    if ((int) $count['cnt'] === 0) {
        $creators = array(
            array('jolie', 'Jolie (졸리)', '보라카이 해변에서 실시간 소통 🏖️', '여행', '보라카이 핫걸 졸리의 라이브', 'https://www.youtube.com/watch?v=jfKfPfyJRdk', 'jfKfPfyJRdk', 1, 1250, 12400, 1200000, 1),
            array('maria', 'Maria', '마닐라 맛집 투어 브이로그 & 먹방 🍜', '먹방', '마닐라 현지 맛집 라이브', '', '', 0, 840, 8200, 125000, 2),
            array('christine', '크리스틴', 'K-POP 댄스 커버 라이브 🎵', '댄스', 'K-POP 댄스 커버 전문', '', '', 1, 3200, 24500, 110000, 3),
        );

        foreach ($creators as $row) {
            sql_query(
                " INSERT INTO `{$creator_table}`
                    (`mc_slug`,`mc_name`,`mc_title`,`mc_category`,`mc_intro`,`mc_stream_url`,`mc_youtube_id`,`mc_is_live`,`mc_viewers`,`mc_followers`,`mc_total_points`,`mc_sort`,`mc_enabled`,`mc_datetime`)
                  VALUES
                    ('" . sql_escape_string($row[0]) . "','" . sql_escape_string($row[1]) . "','" . sql_escape_string($row[2]) . "','" . sql_escape_string($row[3]) . "','" . sql_escape_string($row[4]) . "','" . sql_escape_string($row[5]) . "','" . sql_escape_string($row[6]) . "'," . (int) $row[7] . "," . (int) $row[8] . "," . (int) $row[9] . "," . (int) $row[10] . "," . (int) $row[11] . ",1,'{$now}') ",
                false
            );
        }
    }

    $gift_count = sql_fetch(" SELECT COUNT(*) AS cnt FROM `{$gift_table}` ");
    if ((int) $gift_count['cnt'] === 0) {
        $gifts = array(
            array('heart', '하트', 100, 'Bs', 'text-red-500', 'bg-red-50', 1),
            array('coffee', '커피', 500, 'Gn', 'text-amber-700', 'bg-amber-50', 2),
            array('flower', '꽃다발', 1000, 'Us', 'text-pink-500', 'bg-pink-50', 3),
            array('crown', '왕관', 5000, 'Ws', 'text-yellow-500', 'bg-yellow-50', 4),
        );

        foreach ($gifts as $row) {
            sql_query(
                " INSERT INTO `{$gift_table}`
                    (`mg_key`,`mg_name`,`mg_points`,`mg_icon`,`mg_color`,`mg_bg`,`mg_sort`,`mg_enabled`)
                  VALUES
                    ('" . sql_escape_string($row[0]) . "','" . sql_escape_string($row[1]) . "'," . (int) $row[2] . ",'" . sql_escape_string($row[3]) . "','" . sql_escape_string($row[4]) . "','" . sql_escape_string($row[5]) . "'," . (int) $row[6] . ",1) ",
                false
            );
        }
    }
}

function maganda_format_points($num)
{
    $num = (int) $num;
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    }
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }

    return number_format($num);
}

function maganda_format_rank_points($num)
{
    return number_format((int) $num);
}

function maganda_get_creator_row($id_or_slug)
{
    $table = maganda_table('creator');
    $id_or_slug = trim((string) $id_or_slug);
    if ($id_or_slug === '') {
        return null;
    }

    if (ctype_digit($id_or_slug)) {
        return sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_id` = '" . (int) $id_or_slug . "' AND `mc_enabled` = 1 ");
    }

    return sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_slug` = '" . sql_escape_string($id_or_slug) . "' AND `mc_enabled` = 1 ");
}

function maganda_creator_to_live_item($row)
{
    if (!$row) {
        return null;
    }

    $thumb = $row['mc_cover'] !== '' ? $row['mc_cover'] : 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=800&q=80';
    $profile = $row['mc_avatar'] !== '' ? $row['mc_avatar'] : 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&q=80';

    return array(
        'id' => (int) $row['mc_id'],
        'slug' => $row['mc_slug'],
        'title' => $row['mc_title'],
        'streamer' => $row['mc_name'],
        'viewers' => (int) $row['mc_viewers'],
        'points' => maganda_format_points($row['mc_total_points']),
        'category' => $row['mc_category'],
        'thumb' => $thumb,
        'profile' => $profile,
        'stream_url' => $row['mc_stream_url'],
        'youtube_id' => $row['mc_youtube_id'],
        'intro' => $row['mc_intro'],
        'is_live' => (int) $row['mc_is_live'] === 1,
    );
}

function maganda_creator_to_card_item($row)
{
    $live = maganda_creator_to_live_item($row);
    if (!$live) {
        return null;
    }

    return array(
        'id' => $live['id'],
        'slug' => $live['slug'],
        'name' => $live['streamer'],
        'intro' => $row['mc_intro'],
        'followers' => maganda_format_points($row['mc_followers']),
        'points' => maganda_format_points($row['mc_total_points']),
        'image' => $live['profile'],
        'category' => $live['category'],
        'stream_url' => $live['stream_url'],
        'youtube_id' => $live['youtube_id'],
        'is_live' => $live['is_live'],
    );
}

function maganda_creator_to_room($row)
{
    $live = maganda_creator_to_live_item($row);
    if (!$live) {
        return null;
    }

    return array(
        'id' => $live['id'],
        'slug' => $live['slug'],
        'name' => $live['streamer'],
        'title' => $live['title'],
        'subtitle' => $row['mc_intro'],
        'avatar' => $live['profile'],
        'cover' => $live['thumb'],
        'viewers' => $live['viewers'],
        'total_points' => (int) $row['mc_total_points'],
        'followers' => (int) $row['mc_followers'],
        'stream_url' => $live['stream_url'],
        'youtube_id' => $live['youtube_id'],
        'is_live' => $live['is_live'],
    );
}

function maganda_get_live_list($limit = 8)
{
    $table = maganda_table('creator');
    $limit = max(1, min(20, (int) $limit));
    $result = sql_query(" SELECT * FROM `{$table}` WHERE `mc_enabled` = 1 AND `mc_is_live` = 1 ORDER BY `mc_sort` ASC, `mc_id` ASC LIMIT {$limit} ");
    $list = array();

    while ($row = sql_fetch_array($result)) {
        $item = maganda_creator_to_live_item($row);
        if ($item) {
            $list[] = $item;
        }
    }

    return $list;
}

function maganda_get_creator_cards($limit = 8)
{
    $table = maganda_table('creator');
    $limit = max(1, min(20, (int) $limit));
    $result = sql_query(" SELECT * FROM `{$table}` WHERE `mc_enabled` = 1 ORDER BY `mc_total_points` DESC, `mc_sort` ASC LIMIT {$limit} ");
    $list = array();

    while ($row = sql_fetch_array($result)) {
        $item = maganda_creator_to_card_item($row);
        if ($item) {
            $list[] = $item;
        }
    }

    return $list;
}

function maganda_get_ranking($period = 'daily', $limit = 10)
{
    $period = in_array($period, array('daily', 'weekly', 'monthly'), true) ? $period : 'daily';
    $limit = max(1, min(20, (int) $limit));

    $donation_table = maganda_table('donation');
    $creator_table = maganda_table('creator');
    $interval = array(
        'daily' => '1 DAY',
        'weekly' => '7 DAY',
        'monthly' => '30 DAY',
    );

    $sql = " SELECT d.mb_id, d.mc_id, SUM(d.md_points) AS total_points, c.mc_name
               FROM `{$donation_table}` d
               LEFT JOIN `{$creator_table}` c ON c.mc_id = d.mc_id
              WHERE d.md_datetime >= DATE_SUB(NOW(), INTERVAL {$interval[$period]})
              GROUP BY d.mb_id, d.mc_id
              ORDER BY total_points DESC
              LIMIT {$limit} ";

    $result = sql_query($sql, false);
    $list = array();
    $rank = 1;

    if ($result) {
        while ($row = sql_fetch_array($result)) {
            $nick = $row['mb_id'] !== '' ? $row['mb_id'] : 'Guest';
            if (function_exists('get_member')) {
                $member = get_member($row['mb_id']);
                if ($member && $member['mb_nick'] !== '') {
                    $nick = $member['mb_nick'];
                }
            }

            $list[] = array(
                'rank' => $rank++,
                'user' => $nick,
                'streamer' => $row['mc_name'] !== '' ? $row['mc_name'] : '-',
                'points' => maganda_format_rank_points($row['total_points']),
            );
        }
    }

    if (empty($list)) {
        $fallback = array(
            array(1, '망고나무', '크리스틴', '245,000'),
            array(2, '보라카이신사', 'Jolie (졸리)', '182,500'),
            array(3, '마닐라파파', 'Maria', '98,000'),
        );
        foreach ($fallback as $row) {
            $list[] = array(
                'rank' => $row[0],
                'user' => $row[1],
                'streamer' => $row[2],
                'points' => $row[3],
            );
        }
    }

    return $list;
}

function maganda_get_gifts()
{
    $table = maganda_table('gift');
    $result = sql_query(" SELECT * FROM `{$table}` WHERE `mg_enabled` = 1 ORDER BY `mg_sort` ASC, `mg_id` ASC ");
    $list = array();

    while ($row = sql_fetch_array($result)) {
        $list[] = array(
            'id' => (int) $row['mg_id'],
            'key' => $row['mg_key'],
            'name' => $row['mg_name'],
            'points' => (int) $row['mg_points'],
            'icon' => $row['mg_icon'],
            'color' => $row['mg_color'],
            'bg' => $row['mg_bg'],
        );
    }

    return $list;
}

function maganda_get_chat_messages($creator_id, $limit = 50)
{
    $creator_id = (int) $creator_id;
    $limit = max(1, min(100, (int) $limit));
    $table = maganda_table('chat');
    $result = sql_query(" SELECT * FROM `{$table}` WHERE `mc_id` = {$creator_id} AND `mch_deleted` = 0 ORDER BY `mch_id` DESC LIMIT {$limit} ", false);
    $rows = array();

    if ($result) {
        while ($row = sql_fetch_array($result)) {
            $rows[] = $row;
        }
    }

    $rows = array_reverse($rows);
    $list = array();

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
            $list[] = array(
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

        $list[] = array(
            'id' => (int) $row['mch_id'],
            'type' => 'chat',
            'user' => $user,
            'text' => $row['mch_message'],
            'time' => $time,
        );
    }

    return $list;
}

function maganda_member_payload()
{
    global $member, $is_member;

    if ($is_member && isset($member['mb_id'])) {
        return array(
            'logged' => true,
            'id' => $member['mb_id'],
            'nick' => get_text($member['mb_nick']),
            'points' => (int) $member['mb_point'],
        );
    }

    return array(
        'logged' => false,
        'id' => '',
        'nick' => '',
        'points' => 0,
    );
}

function maganda_urls()
{
    global $g5;

    $member = maganda_member_payload();
    $login_return = urlencode(G5_URL);
    $point_url = G5_BBS_URL . '/point.php';
    $login_for_point = G5_BBS_URL . '/login.php?url=' . urlencode($point_url);

    return array(
        'login' => G5_BBS_URL . '/login.php?url=' . $login_return,
        'register' => G5_BBS_URL . '/register.php',
        'logout' => G5_BBS_URL . '/logout.php',
        'point' => $member['logged'] ? $point_url : $login_for_point,
        'point_charge' => G5_URL . '/page/point-charge.php',
        'my' => G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/member_confirm.php'),
        'apply' => G5_URL . '/page/creator-apply.php',
        'support' => G5_BBS_URL . '/faq.php',
        'contact' => G5_URL . '/page/contact.php',
        'notice' => G5_BBS_URL . '/board.php?bo_table=notice',
        'terms' => G5_URL . '/page/terms.php',
        'privacy' => G5_URL . '/page/privacy.php',
        'refund' => G5_URL . '/page/refund.php',
    );
}

function maganda_analytics_payload()
{
    if (!function_exists('g5site_cfg')) {
        return array();
    }

    return array(
        'gtm_id' => g5site_cfg('gtm_id', ''),
        'ga4_id' => g5site_cfg('ga4_id', ''),
        'meta_pixel_id' => g5site_cfg('meta_pixel_id', ''),
    );
}

function maganda_default_creator_payload()
{
    $table = maganda_table('creator');
    $row = sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_enabled` = 1 ORDER BY `mc_is_live` DESC, `mc_sort` ASC, `mc_id` ASC LIMIT 1 ");

    return maganda_creator_to_room($row);
}

function maganda_json_response($payload, $code = 200)
{
    if (!headers_sent()) {
        http_response_code((int) $code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function maganda_require_member_api()
{
    global $is_member;

    if (!$is_member) {
        maganda_json_response(array(
            'ok' => false,
            'error' => 'login_required',
            'message' => '로그인이 필요합니다.',
        ), 401);
    }
}

function maganda_filter_message($message)
{
    $message = trim(strip_tags((string) $message));
    $message = preg_replace('/\s+/u', ' ', $message);

    if ($message === '') {
        return '';
    }

    $blocked = array('http://', 'https://', 'www.', '.com', '카카오', '텔레그램', '광고');
    $lower = function_exists('mb_strtolower') ? mb_strtolower($message, 'UTF-8') : strtolower($message);

    foreach ($blocked as $word) {
        if (strpos($lower, strtolower($word)) !== false) {
            return '';
        }
    }

    return cut_str($message, 200, '');
}

function maganda_is_following($mb_id, $creator_id)
{
    $table = maganda_table('follow');
    $row = sql_fetch(" SELECT mf_id FROM `{$table}` WHERE `mb_id` = '" . sql_escape_string($mb_id) . "' AND `mc_id` = '" . (int) $creator_id . "' ");

    return $row ? true : false;
}

function maganda_notify_application($application)
{
    if (!function_exists('g5site_cfg')) {
        return;
    }

    $email = g5site_cfg('inquiry_notify_email', g5site_cfg('email', ''));
    if ($email === '') {
        return;
    }

    $subject = '[마간다TV] 방송회원 신청 - ' . $application['ma_name'];
    $body = "이름: {$application['ma_name']}\n";
    $body .= "이메일: {$application['ma_email']}\n";
    $body .= "연락처: {$application['ma_phone']}\n";
    $body .= "YouTube: {$application['ma_youtube']}\n\n";
    $body .= $application['ma_message'];

    if (function_exists('mailer')) {
        mailer($application['ma_name'], $application['ma_email'], $email, $subject, nl2br($body), 0);
    }
}
