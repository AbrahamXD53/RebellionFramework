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
        $getPost = $_REQUEST ?? array();
        $jsonData = json_decode($data, true);
        $jsonData = $jsonData ?? array();
        if(empty($jsonData) and $data){
            self::$request->setRawData($data);
        }
        self::$request->setData(array_merge($getPost, $jsonData));
    }

    public static function getRequest()
    {
        return self::$request;
    }

    public static function getResponse()
    {
        if (!self::$response) {
            self::$response = new Response(self::$request);
            self::$response->setData(array());
        }
        return self::$response;
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
