<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Framework\Router;
use Framework\Session;
Session::start();
require_once "../helpers.php";
$router=new Router();
require_once basePath("routes.php");
$uri=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
$router->route($uri);