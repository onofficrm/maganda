<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

$period = isset($_GET['period']) ? trim($_GET['period']) : 'daily';

maganda_json_response(array(
    'ok' => true,
    'period' => $period,
    'list' => maganda_get_ranking($period),
));
