<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css">', -10);
add_stylesheet('<style>body{margin:0;background:#0f172a;}</style>', -5);

if (!defined('G5_IS_ADMIN')) {
    add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/custom.css">', 0);
    if (function_exists('g5site_cfg')) {
        $mg_primary = g5site_cfg('primary_color', '#2563eb');
        $mg_secondary = g5site_cfg('secondary_color', '#64748b');
        add_stylesheet(
            '<style>:root{--color-primary:' . htmlspecialchars($mg_primary, ENT_QUOTES, 'UTF-8') . ';--color-secondary:'
            . htmlspecialchars($mg_secondary, ENT_QUOTES, 'UTF-8') . ';}</style>',
            1
        );
    }
    add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/g5b-member.css">', 2);
}

add_stylesheet('<link rel="stylesheet" href="' . $member_skin_url . '/style.css">', 5);

if (!function_exists('mg_auth_site_name')) {
    function mg_auth_site_name()
    {
        return function_exists('g5site_cfg') ? g5site_cfg('site_name', '마간다TV') : '마간다TV';
    }
}

if (!function_exists('mg_auth_shell_open')) {
    function mg_auth_shell_open($eyebrow, $title, $desc = '', $extra_class = '')
    {
        $site = mg_auth_site_name();
        echo '<div class="mg-auth ' . htmlspecialchars($extra_class, ENT_QUOTES, 'UTF-8') . '">';
        echo '<div class="mg-auth__bg" aria-hidden="true"></div>';
        echo '<div class="mg-auth__inner">';
        echo '<a class="mg-auth__brand" href="' . G5_URL . '">';
        echo '<span class="mg-auth__brand-mark" aria-hidden="true"></span>';
        echo '<span class="mg-auth__brand-text">' . htmlspecialchars($site, ENT_QUOTES, 'UTF-8') . '</span>';
        echo '</a>';
        echo '<header class="mg-auth-hero">';
        if ($eyebrow !== '') {
            echo '<p class="mg-auth-hero__eyebrow">' . htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        echo '<h1 class="mg-auth-hero__title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
        if ($desc !== '') {
            echo '<p class="mg-auth-hero__desc">' . $desc . '</p>';
        }
        echo '</header>';
        echo '<div class="mg-auth-card">';
    }
}

if (!function_exists('mg_auth_nav')) {
    function mg_auth_nav($active = '')
    {
        $items = array(
            'login'    => array('로그인', G5_BBS_URL . '/login.php'),
            'register' => array('회원가입', G5_BBS_URL . '/register.php'),
            'find'     => array('비밀번호 찾기', G5_BBS_URL . '/password_lost.php'),
        );
        echo '<nav class="mg-auth-nav" aria-label="회원 메뉴">';
        foreach ($items as $key => $item) {
            $cls = ($key === $active) ? ' class="is-active"' : '';
            echo '<a href="' . $item[1] . '"' . $cls . '>' . htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8') . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('mg_auth_shell_close')) {
    function mg_auth_shell_close()
    {
        echo '</div></div></div>';
    }
}
