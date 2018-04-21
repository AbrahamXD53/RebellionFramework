<?php

use Application\Database\Connection;

$params = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'dbname' => 'php7cookbook',
];

$connection = Connection::getInstance($params);