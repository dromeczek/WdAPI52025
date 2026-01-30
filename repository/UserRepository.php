<?php

require_once __DIR__ . '/Repository.php';

class UserRepository extends Repository
{
    public function getUsers(): array
    {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByLogin(string $login): ?array
    {
        $conn = $this->database->connect();

        $stmt = $conn->prepare("SELECT * FROM users WHERE login = :login LIMIT 1");
        $stmt->execute([':login' => $login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function createUser(string $login, string $email, string $passwordHash): void
    {
        $conn = $this->database->connect();

        // pobierz id roli USER
        $roleStmt = $conn->prepare("SELECT id FROM roles WHERE name = 'USER' LIMIT 1");
        $roleStmt->execute();
        $roleId = (int)$roleStmt->fetchColumn();

        if ($roleId === 0) {
            throw new Exception("Brak roli USER w tabeli roles. Dodaj ją w bazie.");
        }

        $stmt = $conn->prepare(
            "INSERT INTO users (login, email, password_hash, role_id)
             VALUES (:login, :email, :password_hash, :role_id)"
        );

        $stmt->execute([
            ':login' => $login,
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':role_id' => $roleId
        ]);
    }
public function getUsersWithHabits(): array {
    $stmt = $this->database->connect()->prepare("
        SELECT 
            u.id, 
            u.login, 
            u.email, 
            u.is_active, -- Dodaliśmy to tutaj
            string_agg(h.name, ', ') as habit_names
        FROM users u
        LEFT JOIN habits h ON u.id = h.user_id
        GROUP BY u.id, u.login, u.email, u.is_active -- I dodaliśmy to tutaj
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function banUser(int $id) {
    $stmt = $this->database->connect()->prepare("
        UPDATE users SET is_active = false WHERE id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}
public function unbanUser(int $id) 
{
    $stmt = $this->database->connect()->prepare("
        UPDATE users SET is_active = true WHERE id = :id
    ");
    $stmt->execute([':id' => $id]);
}
}
