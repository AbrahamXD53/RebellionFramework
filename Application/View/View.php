<?php

class View
{
    public static $view=null;
    
    public static function init()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../views');
        
        /*self::$view= new Twig_Environment($loader, array(
            'cache' => __DIR__ . '\..\..\twig_cache',
        ));*/
        self::$view= new Twig_Environment($loader);
    }

    public static function render($path='',array $params=NULL){
        return self::$view->render($path,$params);
    }
}

View::init();

