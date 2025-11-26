<?php

require_once __DIR__ . '/Repository.php';

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

    public function createUser(string $login, string $plainPassword, string $email): bool
    {
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $conn = $this->database->connect();

        $stmt = $conn->prepare('
            INSERT INTO users (login, password, e_mail)
            VALUES (:login, :password, :email)
        ');

        return $stmt->execute([
            ':login'    => $login,
            ':password' => $hashedPassword,
            ':email'    => $email
        ]);
    }

    public function findByLogin(string $login): ?array
    {
        $conn = $this->database->connect();

        $stmt = $conn->prepare('
            SELECT * FROM users WHERE login = :login
        ');
        $stmt->execute([':login' => $login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
