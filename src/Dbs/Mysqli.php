<?php

namespace Testlin\Db\Dbs;

use Testlin\Db\Dbs\DbInterface;

class Mysqli implements DbInterface
{
    protected $mysqli;

    public function __construct(array $config)
    {
        return $this->getConnection($config);
    }

    public function getConnection(array $config)
    {
        if (is_null($this->mysqli)) {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? '3306';
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8';
            $this->mysqli = new \mysqli($host, $config['username'], $config['password'], $dbname, $port);
            $this->mysqli->set_charset($charset);
        }

        return $this->mysqli;
    }

    public function select_db(String $dbname)
    {
        $this->mysqli->select_db($dbname);
    }

    public function select(String $sql)
    {
        $result = $this->mysqli->query($sql);

        $return = array();
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }

        return $return;
    }

    public function insert(String $table, array $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";

        return (bool) $this->mysqli->query($sql);
    }

    public function getInsertId()
    {
        return (int) $this->mysqli->insert_id;
    }

    public function update(String $table, array $data, $where)
    {
        $where = $where ?: '1';
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "UPDATE {$table} SET {$data} WHERE {$where}";

        return (bool) $this->mysqli->query($sql);
    }

    public function delete(String $table, $where)
    {
        $where = $where ?: '1';
        $sql = "DELETE {$table} WHERE {$where}";

        return (bool) $this->mysqli->query($sql);
    }

    public function beginTransaction()
    {
        $this->mysqli->autocommit(false);
    }

    public function rollback()
    {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);
    }

    public function commit()
    {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
    }
}
