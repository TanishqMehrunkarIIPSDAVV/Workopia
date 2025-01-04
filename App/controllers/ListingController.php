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
     * @param array $params
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
        $newListingData = array_map("sanitize",$newListingData);
        $requiredFields=["title","description","salary","city","state","email"];
        $errors=[];
        foreach($requiredFields as $field)
        {
            if(empty($newListingData[$field]) || !Validation::string($newListingData[$field]))
                $errors[$field] = ucfirst($field)." is  Required!!!";
        }
        if(!empty($errors)) load("listings/create",["errors"=>$errors,"newListingData"=>$newListingData]);
        else
        {
            $fields=[];
            $values=[];
            foreach($newListingData as $field => $value)
            {
                $fields[]=$field;
                if($value === "")
                {
                    $newListingData[$field] = null;
                }
                $values[]=":".$field;
            }
            $fields=implode(", ",$fields);
            $values=implode(", ",$values);
            $this->db->query("INSERT into listings($fields) values($values)",$newListingData);
            redirect("/listings");
        }
    }

    /**
     * Delete a Listing
     * @param array $params
     * @return void
     */
    function destroy($params)
    {
        $id = $params["id"];
        $params=[
            "id"=>$id,
        ];

        $listing=$this->db->query("SELECT * from listings where id = :id",$params)->fetch();
        if(!$listing)
        {
            ErrorController::notFound("Listing Not Found!!!");
            return;
        }
        else
        {
            $this->db->query("DELETE from listings where id = :id",$params);
            $_SESSION["message_success"] = "Listing Deleted Successfully!!!";
            redirect("/listings");
        }
    }
}