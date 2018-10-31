<?php

namespace Testlin\Db\Driver;

class Sqlite implements DbInterface
{
    protected $sqlite;
    protected $sql;

    public function __construct(array $config)
    {
        if (is_null($this->sqlite)) {
            $db_file = isset($config['db_file']) ? $config['db_file'] : '';
            if (file_exists($db_file) === false) {
                throw new \Exception('数据库文件不存在');
            }
            
            $this->sqlite = new \SQLite3($db_file);
            if ($errno = $this->sqlite->lastErrorCode()) {
                throw new \Exception('Connect Error (' . $errno . ') ' . $this->sqlite->lastErrorMsg ());
            }
        }

        return $this->sqlite;
    }

    protected function query($sql)
    {
        $result = $this->sqlite->query($sql);

        if ($errno = $this->sqlite->lastErrorCode()) {
            $error_message = "[Sql Error] {$errno} - ".$this->sqlite->lastErrorMsg();
            throw new \Exception($error_message);
        }

        return $result;
    }

    public function select($sql)
    {
        $result = $this->query($sql);

        $return = array();
        if ($result) {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $return[] = $row;
            }
        }

        return $return;
    }

    public function find($sql)
    {
        $result = $this->query($sql);

        $return = array();
        if ($result) {
            $return = $result->fetchArray(SQLITE3_ASSOC);
        }

        return $return;
    }

    public function getField($sql, $field = null)
    {
        $row = $this->find($sql);
        if (empty($row)) {
            return false;
        }

        if ($field !== null && $field) {
            return $row[$field] ?: false;
        } else {
            $data = array_shift($row);
            return $data ?: false;
        }
    }

    public function insert($table, $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";
        $this->sql = $sql;

        return $this->query($sql);
    }

    public function getInsertId()
    {
        return (int) $this->sqlite->lastInsertRowID();
    }

    public function update($table, $data, $where)
    {
        $update_data = array();
        foreach ($data as $key => $value) {
            $update_data[] = "{$key}='{$value}'";
        }
        if (empty($update_data)) {
            return false;
        }
        $update_data = join(',', $update_data);

        $sql = "UPDATE {$table} SET {$update_data} WHERE {$where}";
        $this->sql = $sql;

        return $this->query($sql);
    }

    public function delete($table, $where)
    {
        $where = $where ? " WHERE {$where} " : '';
        $sql = "DELETE FROM {$table} {$where}";
        $this->sql = $sql;

        return $this->query($sql);
    }

    public function beginTransaction()
    {
        $this->query('BEGIN');
    }

    public function rollback()
    {
        $this->query('ROLLBACK');
    }

    public function commit()
    {
        $this->query('COMMIT');
    }

    public function getSql()
    {
        return $this->sql;
    }
}
