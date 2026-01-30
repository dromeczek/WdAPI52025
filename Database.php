<?php

require_once "config.php";
//todo singleton
class Database {
    private $username;
    private $password;
    private $host;
    private $database;
    
    // Tu będziemy trzymać aktywne połączenie PDO
    private static $connection;

    public function __construct()
    {
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->database = DATABASE;
    }

    public function connect()
    {
        // Jeśli połączenie już istnieje, po prostu je zwróć
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            self::$connection = new PDO(
                "pgsql:host=$this->host;port=5432;dbname=$this->database",
                $this->username,
                $this->password,
                ["sslmode"  => "prefer"]
            );

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$connection;
        }
        catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    //TODO Write disconnect zmienna conecton do pól klasy i zmiana conn
}