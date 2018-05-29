<?php

namespace Testlin\Db;

class Db
{
    protected $db;
    protected $driver;

    public function __construct($driver)
    {
        $driver = strtolower($driver);
        if (in_array($driver, array('pdo', 'mysqli')) == false) {
            throw new \Exception("not driver");
        }
        $this->driver = $driver;
    }

    public function init($config)
    {
        $driver = $this->driver;
        if (!file_exists(__DIR__ . '/Driver/' . ucfirst($driver) . '.php')) {
            throw new \Exception("db driver [$driver] is not supported.");
        }
        $db = __NAMESPACE__ . '\\Driver\\' . ucfirst($driver);
        return new $db($config);
    }
}
