<?php

namespace App\Controllers;

use Framework\Authorization;
use Framework\Database;
use Framework\Session;
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
        $listings = $this->db->query("SELECT * FROM listings order by created_at desc")->fetchAll();
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
        $newListingData["user_id"] = Session::get("user")["id"];
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
            Session::setFlash("message_success","Listing Added Successfully!!!");
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
            if(!Authorization::isOwner($listing->user_id))
            {
                Session::setFlash("message_error","Not Authorized to Delete this Listing!!!");
                return redirect("/listings/$listing->id");
            }
            $this->db->query("DELETE from listings where id = :id",$params);
            Session::setFlash("message_success","Listing Deleted Successfully!!!");
            redirect("/listings");
        }
    }

    /**
     * Show Edit Listing Form
     * @param array $params
     * @return void
     */
    function edit($params)
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
        if(!Authorization::isOwner($listing->user_id))
        {
            Session::setFlash("message_error","Not Authorized to Edit this Listing!!!");
            return redirect("/listings/$listing->id");
        } 
        load("listings/edit",[
            "listing"=> $listing
        ]);
    }

    /**
     * Update a Listing
     * @param array $params
     * @return void
     */
    function update($params)
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
        else
        {
            if(!Authorization::isOwner($listing->user_id))
            {
                Session::setFlash("message_error","Not Authorized to Update this Listing!!!");
                return redirect("/listings/$listing->id");
            } 
            $allowedFields=[
                "title","description","salary","tags","company","address","city","state","phone",
                "email","requirements","benefits",
            ];
    
            $updatedValues=array_intersect_key($_POST,array_flip($allowedFields));
            $updatedValues = array_map("sanitize",$updatedValues);
            $requiredFields=["title","description","salary","city","state","email"];
            $errors=[];
            foreach($requiredFields as $field)
            {
                if(empty($updatedValues[$field]) || !Validation::string($updatedValues[$field]))
                    $errors[$field] = ucfirst($field)." is  Required!!!";
            }
            if(!empty($errors)) load("listings/edit",["errors"=>$errors,"listing"=>$listing]);
            else
            {
                $updatedFields=[];
                foreach(array_keys($updatedValues) as $field)
                {
                    $updatedFields[]="$field = :$field";
                }
                $updatedFields = implode(", ",$updatedFields);
                $sql = "UPDATE listings set $updatedFields where id = :id";
                $params=array_merge($params,$updatedValues);
                $this->db->query($sql,$params);
                Session::setFlash("message_success","Listing Updated!!!");
                redirect("/listings/$id");
            }
        }
    }

    /**
     * Search Listing by Keywords
     * @return void
     */
    function search()
    {
        $keywords = isset($_GET["keywords"]) ? trim($_GET["keywords"]) : "";
        $location = isset($_GET["location"]) ? trim($_GET["location"]) : "";

        $params=[
            "keywords"=>"%$keywords%",
            "location"=>"%$location%",
        ];

        $sql="SELECT * from listings where (
            title like :keywords or
            description like :keywords or
            salary like :keywords or
            tags like :keywords or
            company like :keywords or
            requirements like :keywords or
            benefits like :keywords
        ) and (
            state like :location or
            city like :location
        )";

        $listings = $this->db->query($sql,$params)->fetchAll();

        load("/listings/index",[
            "listings"=>$listings,
            "keywords"=>$keywords,
            "location"=>$location,
        ]);
    }
}