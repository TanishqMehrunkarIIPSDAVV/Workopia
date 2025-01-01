<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once "../helpers.php";
use Framework\Router;

$router=new Router();
require_once basePath("routes.php");
$uri=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
$router->route($uri);