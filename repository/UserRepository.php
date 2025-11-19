<?php

require_once 'Repository.php';
//require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository
{


    public function getUsers(): ?array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users
            ');
     
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}