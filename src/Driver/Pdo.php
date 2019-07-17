<?php

namespace Testlin\Db\Driver;

use Testlin\Db\DBException as Exception;
use PDO as DB;
use PDOException;
use PDOStatement;

class Pdo implements DbInterface
{
    protected $pdo;
    protected $sql;

    public function __construct(array $config)
    {
        if (is_null($this->pdo)) {
            $dbtype = 'mysql';
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? '3306';
            $dbname = $config['dbname'] ?? '';
            $charset = $config['charset'] ?? 'utf8';
            $dsn = "{$dbtype}:host={$host};dbname={$dbname};charset={$charset};port={$port}";

            try {
                $this->pdo = new DB($dsn, $config['username'], $config['password']);
            } catch (PDOException $e) {
                throw new Exception('Connection failed: ' . $e->getMessage());
            }
        }

        return $this->pdo;
    }

    /**
     * @param $sql
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function query($sql)
    {
        $this->sql = $sql;

        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        if ($sth->errorCode() != 0) {
            $error_info = $sth->errorInfo();
            $error_message = "[Sql Error] {$error_info[1]} - {$error_info[2]}";
            throw new Exception($error_message);
        }

        return $sth;
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
        $sth = $this->query($sql);

        $result = $sth->fetchAll(DB::FETCH_ASSOC);

        return $result ? $result : array();
    }

    /**
     * 单行查询
     *
     * @param string $sql
     * @return array|mixed
     * @throws Exception
     */
    public function find(string $sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(DB::FETCH_ASSOC);

        return $result ? $result : array();
    }

    /**
     * 字段查询
     *
     * @param string $sql
     * @param string|null $field
     * @return string
     * @throws Exception
     */
    public function getField(string $sql, string $field = null)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(DB::FETCH_NUM);

        return isset($result['0']) ? $result['0'] : '';
    }

    /**
     * 添加数据
     *
     * @param string $table
     * @param array $data
     * @return bool|string
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
     * 取添加成功后的表 id
     *
     * @return string
     */
    public function getInsertId()
    {
        return $this->pdo->lastInsertId();
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
        $sql = 'DELETE FROM `'.$table.'`';

        if (!is_null($where)) {
            if (is_array($where)) {
                $whereArr = array();
                foreach ($where as $key => $value) {
                    $whereArr[] = '`'.$key.'` = '.$value;
                }
                $sql .= ' where '.implode(' AND ', $whereArr);
            } else {
                $sql .= ' where '.$where;
            }
        }

        return ($this->query($sql) !== false) ? true : false;
    }

    /**
     * 事务开启
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->pdo->rollBack();
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        $this->pdo->commit();
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
