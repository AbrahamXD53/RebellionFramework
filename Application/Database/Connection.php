<?php

namespace Application\Database;

use PDO;

class Connection
{

    public $activeConfig = [];
    const ERROR_UNABLE = 'Error: no database connection';
    private $pdo;
    public static $instance = null;

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    public static function getInstance(array $config = null): Connection
    {
        if (self::$instance) {
            return self::$instance;
        } else {
            self::$instance = new Connection($config);
            return self::$instance;
        }
    }

    public function __construct(array $config)
    {
        $this->activeConfig = $config;
        if (!isset($config['driver'])) {
            $message = __METHOD__ . ' : ' . self::ERROR_UNABLE . PHP_EOL;
            throw new Exception($message);
        }
        $dsn = $this->makeDsn($config);
        try {
            if (isset($config['errmode'])) {
                $this->pdo = new PDO($dsn, $config['user'], $config['pwd'], [PDO::ATTR_ERRMODE => $config['errmode']]);
            } else {
                $this->pdo = new PDO($dsn, $config['user'], $config['pwd'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    public static function factory($drtive, $dbname, $host, $user, $pwd, array $options = [])
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

    public function query($finder, $params = null, $isOperation = false)
    {
        $query = '';
        $result = null;
        if ($finder instanceof Finder) {
            $query = $finder::getSql();
        } elseif (is_string($finder)) {
            $query = $finder;
        }
        try
        {
            if ($isOperation) {
                $this->pdo->beginTransaction();
            }
            $stmt = $this->pdo->prepare($query);
            if ($params) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            if ($isOperation) {
                if ($params) {

                    if (isset($params['id'])) {
                        $result = (int) $params['id'];
                    } else {
                        $result = (int)  $this->pdo->lastInsertId();
                    }
                }

                $this->pdo->commit();
            }
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
            if ($isOperation) {
                $this->pdo->rollBack();
            }
            return null;
        } catch (\Exception $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':' . $e->getMessage());
            return null;
        }

        if ($isOperation) {
            return $result;
        }

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }
}
