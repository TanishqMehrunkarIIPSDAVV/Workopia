<?php
$config = require_once basePath("config/db.php");
$db = new Database($config);

$listings = $db->query("SELECT * FROM listings limit 6")->fetchAll();
load("home",[
    "listings"=>$listings,
]);