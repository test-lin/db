<?php

namespace Testlin\Db\Driver;

use Testlin\Db\DBException as Exception;
use mysqli as DB;
use mysqli_result;

class Mysqli implements DbInterface
{
    protected $mysqli;
    protected $sql;

    public function __construct(array $config)
    {
        if (is_null($this->mysqli)) {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? '3306';
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8';
            $this->mysqli = new DB($host, $config['username'], $config['password'], $dbname, $port);
            if ($this->mysqli->connect_error) {
                throw new Exception('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
            }

            $this->mysqli->set_charset($charset);
        }

        return $this->mysqli;
    }

    /**
     * @param $sql
     * @return bool|mysqli_result
     * @throws Exception
     */
    protected function query($sql)
    {
        $this->sql = $sql;

        $result = $this->mysqli->query($sql);
        if ($this->mysqli->errno) {
            $errorMessage = "[Sql Error] {$this->mysqli->errno} - {$this->mysqli->error}";
            throw new Exception($errorMessage);
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
            while ($row = $result->fetch_assoc()) {
                $return[] = $row;
            }
        }

        return $return;
    }

    /**
     * 单行查询
     *
     * @param string $sql
     * @return array|null
     * @throws Exception
     */
    public function find(string $sql)
    {
        $result = $this->query($sql);

        return $result->fetch_assoc();
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
        $result = $this->query($sql);

        $row = $result->fetch_array();
        if ($field !== null && $field) {
            return $row[$field] ?? false;
        } else {
            return $row[0] ?? false;
        }
    }

    /**
     * 数据添加
     *
     * @param string $table
     * @param array $data
     * @return bool
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
     * 取添加成功后的 id
     *
     * @return int 添加成功后的 id
     */
    public function getInsertId()
    {
        return $this->mysqli->insert_id;
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
        $where = $where ?: '1';
        $sql = "DELETE {$table} WHERE {$where}";

        return ($this->query($sql) !== false) ? true : false;
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->mysqli->autocommit(false);
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);
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
