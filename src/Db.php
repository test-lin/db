<?php

namespace Testlin\Db;

use Testlin\Db\Dbs\DbInterface;

class Db
{
    protected $db;

    public function __construct(string $driver, array $config)
    {
        $driver = strtolower($driver);
        if (in_array($driver, array('pdo', 'mysqli')) == false) {
            throw new Exception("not driver");
        }

        $this->db = $this->dbConnect($driver, $config);
    }

    protected function dbConnect($driver, $config)
    {
        if (!file_exists(__DIR__ . '/Dbs/' . ucfirst($driver) . '.php')) {
            throw new Exception("db driver [$driver] is not supported.");
        }
        $gateway = __NAMESPACE__ . '\\Dbs\\' . ucfirst($driver);
        return new $gateway($config);
    }

    public function getOne($sql)
    {
        $data = $this->db->select($sql);

        $return = null;
        if (isset($data[0])) {
            $return = array_shift($data[0]);
        }

        return $return;
    }

    public function getRow($sql)
    {
        $data = $this->db->select($sql);

        $return = false;
        if (isset($data[0])) {
            $return = $data[0];
        }

        return $return;
    }

    public function getAll($sql)
    {
        $data = $this->db->select($sql);

        return $data;
    }

    public function insert(String $table, array $data)
    {
        $this->db->update($table, $data);
    }

    public function update(String $table, array $data, $where)
    {
        $this->db->update($table, $data, $where);
    }

    public function delete(String $table, $where)
    {
        $this->db->delete($table, $where);
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function rollback()
    {
        $this->db->rollback();
    }

    public function commit()
    {
        $this->db->commit();
    }
}
