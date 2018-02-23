<?php

namespace Testlin\Db\Driver;

use Testlin\Db\Driver\DbInterface;

class PDO implements DbInterface
{
    protected $pdo;
    protected $sql;

    public function __construct(array $config)
    {
        return $this->getConnection($config);
    }

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
        $this->sql = $sql;

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
        $this->sql = $sql;

        return (bool) $this->pdo->query($sql);
    }

    public function delete(String $table, $where)
    {
        $where = $where ?: '1';
        $sql = "DELETE {$table} WHERE {$where}";
        $this->sql = $sql;

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

    public function getSql()
    {
        return $this->sql;
    }
}
