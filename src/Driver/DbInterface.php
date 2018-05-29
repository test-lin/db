<?php

namespace Testlin\Db\Driver;

interface DbInterface
{
    public function select($sql);

    public function find($sql);

    public function getField($sql, $field = null);

    public function insert($table, $data);

    public function getInsertId();

    public function update($table, $data, $where);

    public function delete($table, $where);

    public function beginTransaction();

    public function rollback();

    public function commit();

    public function getSql();
}
