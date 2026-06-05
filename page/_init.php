<?php
/**
 * 서브페이지 공통 부트스트랩
 * - 직접 URL 접근: /page/about.php
 * - include 경로: dirname 기준 프로젝트 루트 _common.php 로드
 */
if (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === '_init.php') {
    exit;
}

if (!defined('_GNUBOARD_')) {
    include_once(dirname(__FILE__).'/../_common.php');
}

if (!defined('_GNUBOARD_')) {
    exit;
}

/**
 * head/tail include 시 GNUBoard 전역 변수 접근 (함수 스코프 include 대응)
 * 별도 함수로 global 선언하면 호출 함수 스코프에 전달되지 않으므로 각 함수 내부에서 직접 선언한다.
 */
function g5_page_enqueue_minimal_assets()
{
    if (defined('G5_IS_ADMIN')) {
        return;
    }

    add_stylesheet('<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css">', -10);
    add_stylesheet('<link rel="stylesheet" href="' . G5_CSS_URL . '/custom.css">', 0);

    if (function_exists('g5site_cfg')) {
        $primary = g5site_cfg('primary_color', '#2563eb');
        $secondary = g5site_cfg('secondary_color', '#64748b');
        add_stylesheet(
            '<style>:root{--color-primary:' . htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') . ';--color-secondary:'
            . htmlspecialchars($secondary, ENT_QUOTES, 'UTF-8') . ';}body{margin:0;}</style>',
            1
        );
    }

    add_javascript('<script src="' . G5_JS_URL . '/custom.js"></script>', 20);
}

/**
 * 서브페이지 시작
 *
 * @param string $title  브라우저·페이지 제목
 * @param string $layout full(기본) | minimal(헤더·사이드·푸터 없음)
 */
function g5_page_start($title, $layout = 'full')
{
    global $g5, $config, $member, $is_member, $is_admin, $is_guest, $board, $group, $default, $g5_debug, $qaconfig;

    $g5['title'] = $title;

    if ($layout === 'minimal') {
        g5_page_enqueue_minimal_assets();
        include_once(G5_PATH . '/head.sub.php');
        return;
    }

    include_once(G5_PATH . '/head.php');
}

/**
 * 서브페이지 종료
 *
 * @param string $layout full(기본) | minimal
 */
function g5_page_end($layout = 'full')
{
    global $g5, $config, $is_admin, $is_member, $g5_debug;

    if ($layout === 'minimal') {
        include_once(G5_PATH . '/tail.sub.php');
        return;
    }

    include_once(G5_PATH . '/tail.php');
}
