<?php
include_once dirname(__DIR__) . '/../../common.php';

if (!function_exists('maganda_bootstrap')) {
    include_once G5_PLUGIN_PATH . '/maganda/maganda.lib.php';
}

maganda_bootstrap();
