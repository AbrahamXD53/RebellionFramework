<?php

use Application\Database\Connection;

$params = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'dbname' => 'rebellion',
];

$connection = Connection::getInstance($params);