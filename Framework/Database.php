<?php

namespace Framework;
use PDO;
use Exception;
use PDOException;

class Database
{
    public $conn;
    /**
     * Constructor
     * @param array $config
     * @return void
     * @throws PDOException
     */
    function __construct($config)
    {
        $dsn="mysql:host={$config["host"]};port={$config["port"]};dbname={$config["dbname"]};charset={$config["charset"]}";
        
        $options=[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try
        {
            $this->conn = new PDO($dsn,$config["username"],$config["password"],$options);
        }
        catch(PDOException $e)
        {
            throw new Exception("Connection Failed!!!! {$e->getMessage()}");
        }
    }

    /**
     * Query
     *
     * @param string $query
     * @param array $params
     * @return PDOStatement
     * @throws PDOException
     */
    function query($query,$params=[])
    {
        try
        {
            $stmt = $this->conn->prepare($query);
            foreach($params as $param => $value)
            {
                $stmt->bindValue(":".$param,$value);
            }
            $stmt->execute();
            return $stmt;   
        }
        catch(PDOException $e)
        {
            throw new Exception("Query Failed!!! {$e->getMessage()}");
        }
    }
}