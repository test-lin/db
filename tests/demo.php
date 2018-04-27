<?php

// require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../init.php';

use Testlin\Db\Db;

$config = [
    'db_type' => 'mysqli',
    'mysqli' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'lin',
        'dbname' => 'test'
    ],
    'pdo' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'lin',
        'dbname' => 'test'
    ]
];
$db_type = $config['db_type'];
$db = new Db($db_type);
$db = $db->init($config[$db_type]);

$sql = "select count(*) from tp_cate";
print_r($db->getField($sql));

echo "\n==============================\n";

print_r($db->find("select * from tp_cate"));

echo "\n==============================\n";

print_r($db->select("select * from tp_cate limit 0, 10"));