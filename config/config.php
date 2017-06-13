<?php

return [
    "db" => include 'db.php',
    'routes' => include 'routes.php',
    'services' => include 'services.php',
    "views" => realpath(dirname(__FILE__) . "/../src/viewsApp"),
    'middlewares' => include 'middlewares.php',
];