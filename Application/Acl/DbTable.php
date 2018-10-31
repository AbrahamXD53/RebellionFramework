<?php

namespace Application\Acl;

use Application\Database\Connection;
use Application\Web\Response;

use PDO;
use Server;

class DbTable implements AuthenticateInterface
{
    const ERROR_AUTH = 'ERROR: authentication error';
    protected $conn;
    protected $table;
    public function __construct($tableName)
    {
        $this->conn = Connection::getInstance();
        $this->table = $tableName;
    }


    public function login():Response
    {
        $params = Server::getRequest()->getData();
        $response = Server::getResponse();
        $response->setStatus(401);
        $response->setData(['error'=>self::ERROR_AUTH]);

        $username = $params['username'] ?? false;
        if ($username) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE username = ?';
            $stmt = $this->conn->getPdo()->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if (password_verify($params['password'], $row['password'])) {
                    unset($row['password']);
                    $response->setStatus(302);
                    $response->setData($row);
                    $sql = 'UPDATE ' . $this->table .' SET last_login = \'' . date('Y-m-d H:i:s') . '\' WHERE id = ?';
                    $stmt = $this->conn->getPdo()->prepare($sql);
                    $stmt->execute([$row['id']]);
                }
            }
        }
        return $response;
    }

    public function loginWithUser($usename,$password):Response
    {
        $response = Server::getResponse();
        $response->setStatus(401);
        $response->setData(['error'=>self::ERROR_AUTH]);

        $username = $usename ?? false;
        if ($username) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE username = ?';
            $stmt = $this->conn->getPdo()->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if (password_verify($password, $row['password'])) {
                    unset($row['password']);
                    $response->setStatus(302);
                    $response->setData($row);
                }
            }
        }
        return $response;
    }
}
