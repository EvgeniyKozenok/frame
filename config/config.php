<?php

return [
//    'request1' => [
//        'driver' => 'mysql',
//        'host' => 'localhost',
//        'dbname' => 'myapp',
//        'user' => 'root',
//        'password' => 'masterJedi'
//    ],
    'routes' => include 'routes.php',
    'services' => include 'services.php',
    "views" => realpath(dirname(__FILE__) . "/../src/viewsApp"),
    'middlewares' => include 'middlewares.php',
];