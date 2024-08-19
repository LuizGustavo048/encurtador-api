<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

return [
    'host' => 'encurtador-fw7-encurtador-3b52.b.aivencloud.com',
    'port' => 22423,
    'dbname' => 'defaultdb',
    'user' => 'avnadmin',
    'password' => 'AVNS_V4ryjRHkGH6rKzzsdN8',
    'sslrootcert' => '/../api/ca.pem',
];
?>