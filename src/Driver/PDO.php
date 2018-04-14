<?php

namespace Testlin\Db\Driver;

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
                $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
            } catch (\PDOException $e) {
                throw new \Exception('Connection failed: ' . $e->getMessage());
            }
        }

        return $this->pdo;
    }

    public function query($sql)
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        if ($sth->errorCode() != 0) {
            $error_info = $sth->errorInfo();
            $error_message = "[Sql Error] {$error_info[1]} - {$error_info[2]}";
            throw new \Exception($error_message);
        }

        return $sth;
    }
    
    public function exec($sql,$lastId = false)
    {
        if($lastId)
        {
            $this->pdo->exec($sql);
            return $this->pdo->lastInsertId();
        }else{
            return $this->pdo->exec($sql);
        }
    }

    public function select(String $sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $result ? $result : array();
    }

    public function find(String $sql)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result : array();
    }

    public function getField(String $sql, String $field = null)
    {
        $sth = $this->query($sql);

        $result = $sth->fetch(\PDO::FETCH_NUM);

        return isset($result['0']) ? $result['0'] : '';
    }

    public function insert(String $table, array $data)
    {
        $fields = "`" . join("`, `", array_keys($data)) . "`";
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$data})";
        $this->sql = $sql;

        return $this->exec($sql, true);
    }

    public function getInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function update(String $table, array $data, $where)
    {
        $where = $where ?: '1';
        $data = "'" . join("', '", array_values($data)) . "'";
        $sql = "UPDATE {$table} SET {$data} WHERE {$where}";
        $this->sql = $sql;

        $rs = $this->exec($sql);

        return ($rs === false) ? false : true;
    }

    public function delete(String $table, $where)
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
        $this->sql = $sql;

        return $this->exec($sql);
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
