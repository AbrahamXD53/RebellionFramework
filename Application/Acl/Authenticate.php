<?php

namespace Application\Acl;

use Server;
use Application\Web\Response;

class Authenticate
{
    public static $instance;

    const ERROR_AUTH = 'ERROR: invalid token';
    const DEFAULT_KEY = 'auth';
    
    protected $adapter;
    protected $token;

    public static function getInstance():Authenticate
    {
        return self::$instance;
    }

    public function __construct(AuthenticateInterface $adapter, $key = null)
    {
        $this->key = $key ?? self::DEFAULT_KEY;
        $this->adapter = $adapter;
        self::$instance=$this;
    }

    public function getToken()
    {
        $this->token = bin2hex(random_bytes(16));
        $_SESSION['token'] = $this->token;
        return $this->token;
    }
    public function matchToken($token)
    {
        $sessToken = $_SESSION['token'] ?? date('Ymd');
        return ($token == $sessToken);
    }

    public function login():Response
    {
        $request = Server::getRequest();
        $params = $request->getData();
        $token = $params['token'] ?? $params->token;

        if (!($token && $this->matchToken($token))) {
            $response = "Error 400";
            $response = Server::getResponse();
            $response->setStatus(400);
            $response->setData(['error' => 'Error 400']);
        } else {
            $response = $this->adapter->login();
        }
        if ($response->getStatus() >= 200 && $response->getStatus() < 303) {
            $_SESSION[$this->key] = $response->getData();
        } else {
            $_SESSION[$this->key] = null;
        }
        return $response;
    }
}
