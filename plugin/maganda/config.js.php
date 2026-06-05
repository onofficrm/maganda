<?php
include_once dirname(__FILE__) . '/../../common.php';

if (!function_exists('maganda_urls')) {
    maganda_json_response(array('ok' => false, 'message' => 'Maganda module unavailable.'), 500);
}

$payload = array(
    'ok' => true,
    'urls' => maganda_urls(),
    'api' => G5_PLUGIN_URL . '/maganda/api/',
    'member' => maganda_member_payload(),
    'analytics' => maganda_analytics_payload(),
    'default_creator' => maganda_default_creator_payload(),
    'sections' => array(
        'live' => 'mg-live',
        'popular' => 'mg-popular',
        'creators' => 'mg-creators',
        'ranking' => 'mg-ranking',
        'support' => 'mg-support',
    ),
);

header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
echo 'window.__MAGANDA__=' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';';
if (!empty($payload['analytics']['gtm_id'])) {
    echo '(function(){var g="' . preg_replace('/[^a-zA-Z0-9-]/', '', $payload['analytics']['gtm_id']) . '";if(!g)return;window.dataLayer=window.dataLayer||[];window.dataLayer.push({"gtm.start":Date.now(),event:"gtm.js"});var s=document.createElement("script");s.async=true;s.src="https://www.googletagmanager.com/gtm.js?id="+g;document.head.appendChild(s);})();';
}
if (!empty($payload['analytics']['ga4_id'])) {
    echo '(function(){var g="' . preg_replace('/[^a-zA-Z0-9-]/', '', $payload['analytics']['ga4_id']) . '";if(!g)return;var s=document.createElement("script");s.async=true;s.src="https://www.googletagmanager.com/gtag/js?id="+g;document.head.appendChild(s);window.dataLayer=window.dataLayer||[];function gt(){dataLayer.push(arguments)}window.gtag=gt;gt("js",new Date());gt("config",g);})();';
}
