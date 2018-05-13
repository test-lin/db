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

echo "\n==============================\n";

$state = $db->update('tp_file', ['name' => 'test', 'type' => 'image/jpeg'], "id = 9");

var_dump($state);

echo "\n==============================\n";

$data = [
    'local_name' => './1/lkwjerlkasdf.mp4',
    'href' => 'http://www.baidu.com/1.mp4',
    'index_id' => 123,
    'is_download' => 1,
];
$state = $db->insert('tp_video', $data);

var_dump($state);

exit;
