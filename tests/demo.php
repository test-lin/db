<?php

require __DIR__ . '/../vendor/autoload.php';

$config = array(
    'dbname' => 'dsc',
    'username' => 'root',
    'password' => 'lin'
);
$mysql = new Testlin\Db\Dbs\MyMysqli;
// $mysql = new Testlin\Db\Dbs\MyPdoMysql;
$mysql->getConnection($config);

$db = new Testlin\Db\Db($mysql);

$sql = "select * from dsc_ad where ad_id=33";
print_r($db->getRow($sql));