<?php

namespace Testlin\Db\Driver;

use Testlin\Db\DBException as Exception;
use SQLite3 as DB;
use SQLite3Result;

class Sqlite implements DbInterface
{
    protected $sqlite;
    protected $sql;

    public function __construct(array $config)
    {
        if (is_null($this->sqlite)) {
            $db_file = isset($config['db_file']) ? $config['db_file'] : '';
            if (file_exists($db_file) === false) {
                throw new Exception('数据库文件不存在');
            }

            $this->sqlite = new DB($db_file);
            if ($errno = $this->sqlite->lastErrorCode()) {
                throw new Exception('Connect Error (' . $errno . ') ' . $this->sqlite->lastErrorMsg());
            }
        }

        return $this->sqlite;
    }

    /**
     * @param $sql
     * @return SQLite3Result
     * @throws Exception
     */
    protected function query($sql)
    {
        $this->sql = $sql;

        $result = $this->sqlite->query($sql);

        if ($errno = $this->sqlite->lastErrorCode()) {
            $error_message = "[Sql Error] {$errno} - ".$this->sqlite->lastErrorMsg();
            throw new Exception($error_message);
        }

        return $result;
    }

    /**
     * 多行查询
     *
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public function select(string $sql)
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

    /**
     * 单选查询
     *
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public function find(string $sql)
    {
        $result = $this->query($sql);

        $return = array();
        if ($result) {
            $return = $result->fetchArray(SQLITE3_ASSOC);
        }

        return $return;
    }

    /**
     * 字段查询
     *
     * @param string $sql
     * @param string|null $field
     * @return bool
     * @throws Exception
     */
    public function getField(string $sql, string $field = null)
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

    /**
     * 添加数据
     *
     * @param string $table
     * @param array $data
     * @return SQLite3Result|bool
     * @throws Exception
     */
    public function insert(string $table, array $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";

        $response = $this->query($sql);
        if ($response !== false) {
            $insertId = $this->getInsertId();
            return (0 < $insertId) ? $insertId : true;
        } else {
            return false;
        }
    }

    /**
     * 取得添加成功后的 id
     *
     * @return int
     */
    public function getInsertId()
    {
        return $this->sqlite->lastInsertRowID();
    }

    /**
     * 更新数据
     *
     * @param string $table
     * @param array $data
     * @param $where
     * @return bool
     * @throws Exception
     */
    public function update(string $table, array $data, $where)
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

        return ($this->query($sql) !== false) ? true : false;
    }

    /**
     * 删除数据
     *
     * @param string $table
     * @param $where
     * @return bool
     * @throws Exception
     */
    public function delete(string $table, $where)
    {
        $where = $where ? " WHERE {$where} " : '';
        $sql = "DELETE FROM {$table} {$where}";

        return ($this->query($sql) !== false) ? true : false;
    }

    /**
     * 开启事务
     *
     * @throws Exception
     */
    public function beginTransaction()
    {
        $this->query('BEGIN');
    }

    /**
     * 回滚事务
     *
     * @throws Exception
     */
    public function rollback()
    {
        $this->query('ROLLBACK');
    }

    /**
     * 提交事务
     *
     * @throws Exception
     */
    public function commit()
    {
        $this->query('COMMIT');
    }

    /**
     * 取最后执行 sql
     *
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }
}
