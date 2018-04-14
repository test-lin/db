<?php

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/../init.php';

$config = [
    'db_type' => 'mysqli',
    'mysqli' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'test'
    ],
    'pdo' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'test'
    ]
];
$db_type = $config['db_type'];
$db = new Testlin\Db\Db($db_type, $config[$db_type]);

$sql = "select * from dsc_ad limit 1";
print_r($db->getRow($sql));