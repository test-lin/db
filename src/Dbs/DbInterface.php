<?php

namespace Testlin\Db\Dbs;

interface DbInterface
{
    public function getConnection(array $config);

    public function select(String $sql);

    public function insert(String $table, array $data);

    public function update(String $table, array $data, $where);

    public function delete(String $table, $where);

    public function beginTransaction();

    public function rollback();

    public function commit();
}
