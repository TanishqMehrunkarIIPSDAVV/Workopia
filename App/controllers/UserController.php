<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;

class UserController
{
    protected $db;

    function __construct()
    {
        $config = require_once basePath("/config/db.php");
        $this->db = new Database($config);
    }

    /**
     * Login Page
     * @return void
     */
    function login()
    {
        load("users/login");
    }

    /**
     * Register Page
     * @return void
     */
    function create()
    {
        load("users/register");
    }

    /**
     * Store a User
     * 
     */
    function store()
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $password = $_POST["password"];
        $password_confirmation = $_POST["password_confirmation"];

        $errors=[];

        if(!Validation::email($email)) $errors["email"] = "Please Enter a Valid Email Address!!!";
        if(!Validation::string($name,2,50)) $errors["name"] = "Name length should be between 2 to 50 characters!!!";
        if(!Validation::string($city,2,50)) $errors["city"] = "City length should be between 2 to 50 characters!!!";
        if(!Validation::string($state,2,50)) $errors["state"] = "State length should be between 2 to 50 characters!!!";
        if(!Validation::string($password,6,50)) $errors["password"] = "Password length should be between 6 to 50 characters!!!";
        if(!Validation::match($password,$password_confirmation)) $errors["password_confirmation"] = 'Passwords Do Not Match!!!';
        if(!empty($errors)) load("users/register",
        [
            "errors"=>$errors,
            "user"=>
            [
                "name" => $name,
                "email" => $email,
                "city" => $city,
                "state" => $state,
            ]
        ]);
        else
        {
            $params=[
                "email"=>$email,
            ];
            $user = $this->db->query("SELECT * from users where email = :email",$params)->fetch();
            if($user)
            {
                $errors["email"] = "Email is Already Registered!!!";
                load("users/register",
                [
                    "errors"=>$errors,
                    "user"=>
                    [
                        "name" => $name,
                        "email" => $email,
                        "city" => $city,
                        "state" => $state,
                    ]
                ]);
                exit;
            }
            $params=[
                "name"=>$name,
                "email"=>$email,
                "city"=>$city,
                "state"=>$state,
                "password"=>password_hash($password,PASSWORD_BCRYPT),
            ];
            $this->db->query("INSERT into users(name,email,city,state,password)
            values(:name, :email, :city, :state, :password)",$params);

            $userId = $this->db->conn->lastInsertId();
            Session::set("user",[
                "id"=>$userId,
                "name"=>$name,
                "email"=>$email,
                "city"=>$city,
                "state"=>$state,
            ]);

            redirect("/");
        }
    }

    /**
     * Logout A User
     * @return void
     */
    function logout()
    {
        Session::destroy();
        $params=session_get_cookie_params();
        setcookie("PHPSESSID","",time() - 86400, $params["path"],$params["domain"]);
        redirect("/");
    }

    /**
     * Authenticate a User
     * @return void
     */
    function authenticate()
    {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $errors=[];

        if(!Validation::email($email)) $errors["email"] = "Please Enter a Valid Email!!!";
        if(!Validation::string($password,6,50)) $errors["password"] = "Password length should be between 6 to 50 characters!!!";
        if(!empty($errors))
        {
            load("users/login",["errors"=>$errors]);
            exit;
        }

        $params=[
            "email"=>$email,
        ];

        $user = $this->db->query("SELECT * from users where email = :email",$params)->fetch();
        if($user)
        {
            if(!password_verify($password,$user->password))
            {
                $errors["email"] = "Incorrect Credentials!!!";
                load("users/login",["errors"=>$errors]);
                exit;
            }
            Session::set("user",[
                "id"=>$user->id,
                "name"=>$user->name,
                "email"=>$user->email,
                "city"=>$user->city,
                "state"=>$user->state,
            ]);
            redirect("/");
        }
        $errors["email"] = "Incorrect Credentials!!!";
        load("users/login",["errors"=>$errors]);
        exit;
    }
}