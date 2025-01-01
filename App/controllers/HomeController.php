<?php

namespace App\Controllers;
use Framework\Database;

class HomeController
{
    protected $db;
    public function __construct()
    {
        $config = require_once basePath("config/db.php");
        $this->db = new Database($config);
    }

    /**
     * Home Page
     * @return void
     */
    function index()
    {
        $listings = $this->db->query("SELECT * FROM listings limit 6")->fetchAll();
        load("home",[
            "listings"=>$listings,
        ]);
    }
}