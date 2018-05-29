<?php

namespace Testlin\Db\Driver;

class Mysqli implements DbInterface
{
    protected $mysqli;
    protected $sql;

    public function __construct(array $config)
    {
        if (is_null($this->mysqli)) {
            $host = isset($config['host']) ? $config['host'] : '127.0.0.1';
            $port = isset($config['port']) ? $config['port'] : '3306';
            $dbname = isset($config['dbname']) ? $config['dbname'] : '';
            $charset = isset($config['charset']) ? $config['charset'] : 'utf8';
            $this->mysqli = new \mysqli($host, $config['username'], $config['password'], $dbname, $port);
            if ($this->mysqli->connect_error) {
                throw new \Exception('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
            }

            $this->mysqli->set_charset($charset);
        }

        return $this->mysqli;
    }

    public function select_db(String $dbname)
    {
        $this->mysqli->select_db($dbname);
    }

    protected function query($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($this->mysqli->errno) {
            $error_message = "[Sql Error] {$this->mysqli->errno} - {$this->mysqli->error}";
            throw new \Exception($error_message);
        }

        return $result;
    }

    public function select($sql)
    {
        $result = $this->query($sql);

        $return = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $return[] = $row;
            }
        }

        return $return;
    }

    public function find($sql)
    {
        $result = $this->query($sql);

        return $result->fetch_assoc();
    }

    public function getField($sql, $field = null)
    {
        $result = $this->query($sql);

        $row = $result->fetch_array();
        if ($field !== null && $field) {
            return $row[$field] ?: false;
        } else {
            return $row[0] ?: false;
        }
    }

    public function insert($table, $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";
        $this->sql = $sql;

        return (bool) $this->query($sql);
    }

    public function getInsertId()
    {
        return (int) $this->mysqli->insert_id;
    }

    public function update($table, $data, $where)
    {
        // $where = $where ?: '1';
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "UPDATE {$table} SET {$data} WHERE {$where}";
        $this->sql = $sql;

        return (bool) $this->query($sql);
    }

    public function delete($table, $where)
    {
        $where = $where ?: '1';
        $sql = "DELETE {$table} WHERE {$where}";
        $this->sql = $sql;

        return (bool) $this->query($sql);
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

    public function getSql()
    {
        return $this->sql;
    }
}
