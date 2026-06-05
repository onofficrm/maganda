<?php
include_once dirname(__FILE__) . '/_common.php';

maganda_bootstrap();

$live = maganda_get_live_list(8);
$creators = maganda_get_creator_cards(8);
$default_creator = maganda_default_creator_payload();

maganda_json_response(array(
    'ok' => true,
    'live' => $live,
    'creators' => $creators,
    'ranking' => array(
        'daily' => maganda_get_ranking('daily'),
        'weekly' => maganda_get_ranking('weekly'),
        'monthly' => maganda_get_ranking('monthly'),
    ),
    'default_creator' => $default_creator,
));
