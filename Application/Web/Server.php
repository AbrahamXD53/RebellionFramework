<?php

use Application\Web\Request;
use Application\Web\Response;

class Server
{
    private static $request = null;
    private static $response = null;

    public static function init()
    {
        $data=file_get_contents('php://input');
        self::$request = new Request();
        $getPost = $_REQUEST ?? [];
        $jsonData = json_decode($data, true);
        $jsonData = $jsonData ?? [];
        if(empty($jsonData) and $data){
            self::$request->setRawData($data);
        }
        self::$request->setData(array_merge($getPost, $jsonData));
    }

    public static function getRequest():Request
    {
        return self::$request;
    }

    public static function getResponse():Response
    {
        if (!self::$response) {
            self::$response = new Response(self::$request);
            self::$response->setData([]);
        }
        return self::$response;
    }

    public static function abort($code){
        if (!self::$response) {
            self::$response = new Response(self::$request);
            self::$response->setData([]);
        }
        self::$response->setData($code);
        self::$response->setStatus($code);
    }

    public static function processResponse()
    {
        if (self::$response) {
            if (self::$response->getHeaders()) {
                foreach (self::$response->getHeaders() as $key => $value) {
                    header($key . ': ' . $value, true, self::$response->getStatus());
                }
            }
            header(Request::HEADER_CONTENT_TYPE . ': ' . Request::CONTENT_TYPE_JSON, true);
            if (self::$response->getCookies()) {
                foreach (self::$response->getCookies() as $key => $value) {
                    setcookie($key, $value);
                }
            }
        }
    }
}
Server::Init();
