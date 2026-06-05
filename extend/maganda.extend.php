<?php
/**
 * Maganda TV 플러그인 — onoff-g5-base 연동
 */
if (!defined('_GNUBOARD_')) {
    exit;
}

if (is_file(G5_PATH . '/_site.config.php')) {
    include_once G5_PATH . '/_site.config.php';
}

if (function_exists('g5site_cfg_bool') && !g5site_cfg_bool('maganda_builtin', true)) {
    return;
}

$maganda_lib = G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
if (!is_file($maganda_lib)) {
    return;
}

include_once $maganda_lib;

if (function_exists('maganda_bootstrap')) {
    maganda_bootstrap();
}

if (function_exists('add_replace')) {
    add_replace('admin_menu', 'maganda_admin_menu', 20, 1);
}

function maganda_admin_menu($admin_menu)
{
    if (!defined('G5_PLUGIN_URL')) {
        return $admin_menu;
    }

    $admin_menu['menu200'][] = array('200920', '마간다TV 관리', G5_PLUGIN_URL . '/maganda/admin/index.php', 'maganda');

    return $admin_menu;
}

if (!function_exists('onoff_builder_render_filter_html')) {
    function onoff_builder_render_filter_html($html, $id, $meta = array())
    {
        if ($id !== 'maganda' || !defined('G5_PLUGIN_URL')) {
            return $html;
        }

        $config_src = G5_PLUGIN_URL . '/maganda/config.js.php';
        $inject = '<script src="' . htmlspecialchars($config_src, ENT_QUOTES, 'UTF-8') . '"></script>';

        if (stripos($html, 'maganda/config.js.php') === false) {
            $html = preg_replace('#</head>#i', $inject . "\n</head>", $html, 1);
        }

        if (function_exists('g5site_cfg')) {
            $site_name = g5site_cfg('site_name', 'Maganda TV');
            $html = preg_replace('#<html lang="[^"]*">#i', '<html lang="ko">', $html, 1);
            if (stripos($html, '<title>') !== false) {
                $html = preg_replace('#<title>.*?</title>#is', '<title>' . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . '</title>', $html, 1);
            }
        }

        return $html;
    }
}

if (function_exists('g5site_cfg_bool') && g5site_cfg_bool('member_skin_maganda', true)) {
    $maganda_member_skin = 'maganda';
    $mg_skin_base = G5_IS_MOBILE ? G5_MOBILE_PATH : G5_PATH;
    $mg_skin_url_base = G5_IS_MOBILE ? G5_MOBILE_URL : G5_URL;
    $mg_member_skin_path = $mg_skin_base . '/' . G5_SKIN_DIR . '/member/' . $maganda_member_skin;
    if (is_dir($mg_member_skin_path)) {
        $member_skin_path = $mg_member_skin_path;
        $member_skin_url = $mg_skin_url_base . '/' . G5_SKIN_DIR . '/member/' . $maganda_member_skin;
    }
}
