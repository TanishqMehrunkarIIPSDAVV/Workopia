<?php
require_once "../helpers.php";
require_once basePath("Database.php");
require_once basePath("Router.php");

$router=new Router();
require_once basePath("routes.php");
$uri=$_SERVER["REQUEST_URI"];
$method=$_SERVER["REQUEST_METHOD"];
$router->route($uri,$method);