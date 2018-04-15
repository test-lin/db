# 数据库

## 环境
* php >= 7.0
* mysqli 或 pdo 支持
* composer

## 配置
```php
$config = [
    'db_type' => 'mysqli', // mysqli or pdo
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
```
## 操作

所有驱动均支持以下方法

- select(String $sql);  
说明：查询多条数据  
参数：执行的 sql  
返回：查询成功，返回 二维数组数据；否则返回 false；  

- find(String $sql);  
说明：查询一条数据  
参数：执行的 sql  
返回：查询成功，返回 关联数组数据；否则返回 false；

- getField(String $sql, String $field = null);  
说明：取一个字段的值  
参数：执行的 sql，指定字段  
返回：查询成功，返回 字段数据；否则返回 false；

- insert(String $table, array $data);  
说明：添加数据到数据表  
参数：表名，添加到数据库的数据  
返回：bool

- update(String $table, array $data, $where);  
说明：更新数据表数据  
参数：表名，要修改的数据，条件  
返回：bool

- delete(String $table, $where);  
说明：删除数据表数据  
参数：表名，条件  
返回：bool

- beginTransaction();  
说明：开启事务  
返回：null

- rollback();  
说明：事务回滚  
返回：null

- commit();  
说明：提交事务  
返回：null

## 实例
```php
$db_type = $config['db_type'];
$db = new Testlin\Db\Db($db_type, $config[$db_type]);
```