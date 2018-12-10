<?php
/**
 * Created by PhpStorm.
 * User: sanzhar
 * Date: 06.12.18
 * Time: 10:03
 */
require(__DIR__ . '/../vendor/autoload.php');

$config = require(__DIR__ . '/../config/main.php');

$app = new App\services\Application($config);
$app->run();