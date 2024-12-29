<?php
require_once "../helpers.php";
require_once basePath("Database.php");
require_once basePath("Router.php");

$router=new Router();
require_once basePath("routes.php");
$uri=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
$method=$_SERVER["REQUEST_METHOD"];
$router->route($uri,$method);