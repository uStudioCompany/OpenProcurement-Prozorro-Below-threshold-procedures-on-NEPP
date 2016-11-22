<?php
return [
    'class' => 'app\components\opAPI',
    'url' => 'https://lb.api-sandbox.openprocurement.org/api/',
    'ds_upload_url' => 'https://upload.docs-sandbox.openprocurement.org/',
//    'url' => 'https://lb.api.openprocurement.org/api/',
    'version' => '2.3',
    'auth_key' => base64_encode(':'),
    'ds_auth_key' => '=',
    'cookie_file' => __DIR__ . '/cookies.txt',
    'ssl_verify' => false,
];