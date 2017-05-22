<?php
use John\Frame\Application;

$loader = require '../vendor/autoload.php';

$app = new Application((include dirname(__FILE__) . "/../config/config.php"), dirname(__FILE__) . "/logs");
$app->start();
