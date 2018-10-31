<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Application/Autoload/Loader.php';

Application\Autoload\Loader::init([__DIR__]);

require_once __DIR__ . '/Application/Config/Database.config.php';
require_once __DIR__ . '/Application/Web/Server.php';
require_once __DIR__ . '/Application/View/View.php';
require_once __DIR__ . '/Application/Entity/Loader.php';
require_once __DIR__ . '/Application/Router/Router.php';

Application\Acl\Auth::init();
Application\Router\Router::init('http://localhost/terminus/');

require_once __DIR__ . '/routes/web.php';

Application\Router\Router::dispatch();