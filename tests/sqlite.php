<?php

require __DIR__ . '/../init.php';

use Testlin\Db\Db;

$config = [
    'db_type' => 'sqlite',
    'sqlite' => [
        'db_file' => './test.db'
    ]
];

$db_type = $config['db_type'];
$db = new Db($db_type);
$db = $db->init($config[$db_type]);


$sql = "select * from category";
print_r($db->select($sql));

echo "\n==============================\n";

print_r($db->find("SELECT * FROM category WHERE id=5"));

echo "\n==============================\n";

print_r($db->getField("SELECT tag FROM tag WHERE id=2"));

echo "\n==============================\n";

$data = array(
    'name' => 'composer testing',
    'pid' => 1,
    'created' => time()
);
var_dump($db->insert('category', $data));

echo "\n==============================\n";

try {
    $db->beginTransaction();

    $data = array(
        'name' => '23456789',
        'pid' => 1,
        'created' => time(),
    );
    $start = $db->insert('category', $data);
    if ($start === false) {
        throw new Exception("添加失败");
    }

    $id = $db->getInsertId();
    
    $data = array(
        'name' => '23456789',
        'pid' => 1
    );
    $start = $db->update('category', $data, "id={$id}");
    if ($start === false) {
        throw new Exception("更新失败");
    }

    $start = $db->delete('category', "id={$id}");
    if ($start === false) {
        throw new Exception("删除失败");
    }

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    var_dump($e->getMessage());
}
