<?php

namespace Testlin\Db\Dbs;

use Testlin\Db\Dbs\DbInterface;

class MyPdoMysql implements DbInterface
{
    protected $pdo;

    public function getConnection(array $config)
    {
        if (is_null($this->pdo)) {
            $dbtype = 'mysql';
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? '3306';
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8';
            $dsn = "{$dbtype}:host={$host};dbname={$dbname};charset={$charset};port={$port}";
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
        }

        return $this->pdo;
    }

    public function select(String $sql)
    {
        $result = $this->pdo->query($sql);
        $return = array();
        foreach ($result as $row) {
            $return[] = $row;
        }

        return $return;
    }

    public function insert(String $table, array $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";

        return (bool) $this->pdo->query($sql);
    }

    public function getInsertId()
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function update(String $table, array $data, $where)
    {
        $where = $where ?: '1';
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "UPDATE {$table} SET {$data} WHERE {$where}";

        return (bool) $this->pdo->query($sql);
    }

    public function delete(String $table, $where)
    {
        $where = $where ?: '1';
        $sql = "DELETE {$table} WHERE {$where}";

        return (bool) $this->pdo->query($sql);
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }

    public function commit()
    {
        $this->pdo->commit();
    }
}
