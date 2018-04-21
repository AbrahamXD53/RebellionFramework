<?php

namespace Application\Database;

use Application\Database\{Connection,Finder};
use PDOException;

class Paginate
{

    const DEFAULT_LIMIT = 20;
    const DEFAULT_OFFSET = 0;

    protected $sql;
    protected $page;
    protected $linesPerPage;

    public function __construct($sql, $page, $linesPerPage)
    {
        $offset = $page * $linesPerPage;
        if ($sql instanceof Finder) {
            $sql->limit($linesPerPage);
            $sql->offset($offset);
            $this->sql = $sql::getSql();
        } elseif (is_string($sql)) {
            switch (true) {
                case (stripos($sql, 'LIMIT') && strpos($sql, 'OFFSET')):
                    break;
                case (stripos($sql, 'LIMIT')):
                    $sql .= ' LIMIT ' . self::DEFAULT_LIMIT;
                    break;
                case (stripos($sql, 'LIMIT')):
                    $sql .= ' OFFSET ' . self::DEFAULT_OFFSET;
                    break;
                default:
                    $sql .= ' LIMIT ' . self::DEFAULT_LIMIT;
                    $sql .= ' OFFSET ' . self::DEFAULT_OFFSET;
                    break;
            }
        }
        $this->sql = preg_replace('/LIMIT \d+.*OFFSET \d+/Ui',
            'LIMIT ' . $linesPerPage . ' OFFSET ' . $offset,
            $sql);
    }

    public function paginate(Connection $connection, $fetchMode, $params = array())
    {
        try {
            $stmt = $connection->pdo->prepare($this->sql);

            if (!$stmt) {
                return false;
            }

            if ($params) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            while ($result = $stmt->fetch($fetchMode)) {
                yield $result;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getSql()
    {
        return $this->sql;
    }

}
