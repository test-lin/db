<?php

namespace Testlin\Db\Driver;

interface DbInterface
{
    public function select(String $sql);

    public function find(String $sql);

    public function getField(String $sql, String $field = null);

    public function insert(String $table, array $data);

    public function getInsertId();

    public function update(String $table, array $data, $where);

    public function delete(String $table, $where);

    public function beginTransaction();

    public function rollback();

    public function commit();

    public function getSql();
}
