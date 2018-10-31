<?php

namespace Application\Router;

use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;

class Router
{

    private static $router;
    private static $baseUrl;

    public static function getCurrentUri()
    {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    public static function init($baseUrl)
    {
        self::$baseUrl = $baseUrl;
        self::$router = new RouteCollector();

    }

    public static function dispatch()
    {
        $dispatcher = new Dispatcher(self::$router->getData());

        try {

            $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], self::getCurrentUri());
            \Server::processResponse();
            echo $response;

        } catch (HttpRouteNotFoundException $e) {

            echo \View::render('errors/error.html', ['code' => 404, 'description' => $e->getMessage()]);
            die();

        } catch (HttpMethodNotAllowedException $e) {

            echo \View::render('errors/error.html', ['code' => 405, 'description' => $e->getMessage()]);
            die();

        } catch (\Exception $e) {
            echo \View::render('errors/error.html', ['code' => 500, 'description' => $e->getMessage()]);
            die();
        }

    }

    public static function redirect($route)
    {
        header('Location: ' . self::$baseUrl . $route);
        die();
    }

    public static function filter($name,$function){
        self::$router->filter($name,$function);
    }

    public static function group(array $setup,$function){
        self::$router->group($setup,$function);
    }

    public static function controller($path, $class)
    {
        self::$router->controller($path, $class);
    }

    public static function get($path, $callback)
    {
        self::$router->get($path, $callback);
    }
    public static function post($path, $callback)
    {
        self::$router->post($path, $callback);
    }
    public static function put($path, $callback)
    {
        self::$router->put($path, $callback);
    }
    public static function any($path, $callback)
    {
        self::$router->any($path, $callback);
    }

}
