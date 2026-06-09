<?php
// Standalone geo endpoint — no WordPress bootstrap.
// Called by cookie-consent.js to determine EU/EEA visitor status.
// Browser caches per-user for 1 hour (private); CDN never caches.

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=3600');
header('X-Robots-Tag: noindex, nofollow');

$eu_countries = [
    'AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR',
    'HU','IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK',
    'IS','LI','NO',
    'GB','CH',
];

$country = '';
if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
    $country = strtoupper(preg_replace('/[^A-Z0-9]/', '', strtoupper($_SERVER['HTTP_CF_IPCOUNTRY'])));
}

$is_eu = $country === 'T1' || in_array($country, $eu_countries, true);

echo json_encode(['eu' => $is_eu]);
