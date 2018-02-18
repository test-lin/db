<?php

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/../init.php';

$config = array(
    'dbname' => 'dsc',
    'username' => 'root',
    'password' => 'lin'
);
$db = new Testlin\Db\Db('pdo', $config);

$sql = "select * from dsc_ad limit 1";
print_r($db->getRow($sql));