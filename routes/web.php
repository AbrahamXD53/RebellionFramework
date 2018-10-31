<?php

use Application\Controller\UserController;
use Application\Router\Router;

Router::filter('auth', function () {
    if (!isset($_SESSION['auth'])) {
        Router::redirect('login');
    }
});

Router::filter('no-auth', function () {
    if (isset($_SESSION['auth'])) {
        Router::redirect('/');
    }
});

Router::group(['before' => 'auth'], function () {
    Router::get('/', function () {
        Router::redirect('user/game');
    });
    Router::get('/logout', function () {
        if (isset($_SESSION['auth'])) {
            $_SESSION['auth'] = null;
        }
        Router::redirect('login');
    });
    Router::controller('/user', UserController::class);
});

Router::group(['before' => 'no-auth'], function () {
    
});
