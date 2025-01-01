<?php

namespace App\Controllers;
use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;
    public function __construct()
    {
        $config = require_once basePath("config/db.php");
        $this->db = new Database($config);
    }
    
    /**
     * Show All Listings
     * @return void
     */
    function index()
    {
        $listings = $this->db->query("SELECT * FROM listings")->fetchAll();
        load("listings/index",[
            "listings"=>$listings,
        ]);
    }

    /**
     * Create a Listing
     * @return void
     */
    function create()
    {
        load("listings/create");
    }

    /**
     * Show a Detailed Listing
     * @return void
     */
    function show($params)
    {
        $id = $params["id"] ?? "";
        $params = [
            "id" => $id,
        ];
        $listing = $this->db->query("SELECT * FROM listings where id = :id", $params)->fetch();
        if(!$listing)
        {
            ErrorController::notFound("Listing Not Found!!!");
            return;
        }
        load("listings/show",[
            "listing" => $listing
        ]);
    }

    /**
     * Store Data
     * @return void
     */
    function store()
    {
        $allowedFields=[
            "title","description","salary","tags","company","address","city","state","phone",
            "email","requirements","benefits",
        ];

        $newListingData=array_intersect_key($_POST,array_flip($allowedFields));
        $newListingData["user_id"] = 1;
        inspectDie($newListingData);
    }
}