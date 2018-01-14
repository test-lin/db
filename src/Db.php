<?php

namespace Testlin\Db;

use Testlin\Db\Dbs\DbInterface;

class Db
{
    protected $db;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
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
