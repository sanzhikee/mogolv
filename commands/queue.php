<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 23:43
 */

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../services/Application.php');

$config = require(__DIR__ . '/../config/main.php');

$app = new App\services\Application($config);
$app->queue();