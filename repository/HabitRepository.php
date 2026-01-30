<?php

require_once __DIR__ . '/Repository.php';

class HabitRepository extends Repository
{
    public function getHabits(int $userId): array
    {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addHabit(string $name, int $userId): void
    {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("
            INSERT INTO habits (name, user_id, current_health) 
            VALUES (:name, :user_id, 50)
        ");
        $stmt->execute([
            ':name' => $name,
            ':user_id' => $userId
        ]);
    }
}