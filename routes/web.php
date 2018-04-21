<?php

$router->filter('auth', function () {
    if (!isset($_SESSION['auth'])) {
        header("Location: http://localhost/oop/login");
        die();
    }
});

$router->group(['before' => 'auth'], function ($router) {
    $router->get('/', function () {
        return View::render('index.html', array('title' => 'index', 'content' => json_encode($_SESSION['auth'])));
    });

    $router->controller('/', Application\Controller\CustomerController::class);
});
