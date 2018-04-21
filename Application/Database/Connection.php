<?php

namespace Application\Database;

use PDO;

class Connection
{

    const ERROR_UNABLE = 'Error: no database connection';
    public $pdo;
    public static $instance=NULL;

    public static function getInstance(array $config=NULL)
    {
        if(self::$instance){
            return self::$instance;
        }
        else
        {
            self::$instance=new Connection($config);
            return self::$instance;
        }
    }

    public function __construct(array $config)
    {
        if (!isset($config['driver'])) {
            $message = __METHOD__ . ' : ' . self::ERROR_UNABLE . PHP_EOL;
            throw new Exception($message);
        }
        $dsn = $this->makeDsn($config);
        try {
            if (isset($config['errmode'])) {
                $this->pdo = new PDO($dsn, $config['user'], $config['pwd'], [PDO::ATTR_ERRMODE => $config['errmode']]);
            } else {
                $this->pdo = new PDO($dsn, $config['user'], $config['pwd'],[PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    public static function factory($drtive, $dbname, $host, $user, $pwd, array $options = array())
    {
        $dsn = sprintf('%s:dbname=%s;host=%s', $driver, $dbname, $host);
        try {
            return new PDO($dsn, $user, $pwd, $options);
        } catch (PDOException $e) {
            error_log($e->getMessage);
        }
    }

    public function makeDsn($config)
    {
        $dsn = $config['driver'] . ':';

        unset($config['driver']);
        foreach ($config as $key => $value) {
            $dsn .= $key . '=' . $value . ';';
        }

        return substr($dsn, 0, -1);
    }
}
