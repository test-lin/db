<?php

namespace Testlin\Db\Driver;

interface DbInterface
{
    public function select(string $sql);

    public function find(string $sql);

    public function getField(string $sql, string $field = null);

    public function insert(string $table, array $data);

    public function getInsertId();

    public function update(string $table, array $data, $where);

    public function delete(string $table, $where);

    public function beginTransaction();

    public function rollback();

    public function commit();

    public function getSql();
}
