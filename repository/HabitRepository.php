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
    public function refreshHabitsHealth(int $userId): void
{
    $conn = $this->database->connect();
    
    // Pobierz wszystkie nawyki użytkownika
    $habits = $this->getHabits($userId);

    foreach ($habits as $habit) {
        // Sprawdź datę ostatniego podlania
        $stmt = $conn->prepare("
            SELECT MAX(date) FROM habit_logs WHERE habit_id = :habit_id
        ");
        $stmt->execute([':habit_id' => $habit['id']]);
        $lastWatering = $stmt->fetchColumn();

        if ($lastWatering) {
            $today = new DateTime();
            $lastDate = new DateTime($lastWatering);
            $daysDiff = $today->diff($lastDate)->days;

            if ($daysDiff > 0) {
                // Kara: -20 pkt za każdy dzień bez podlewania
                $damage = $daysDiff * 20;
                $stmt = $conn->prepare("
                    UPDATE habits 
                    SET current_health = GREATEST(0, current_health - :damage) 
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':damage' => $damage,
                    ':id' => $habit['id']
                ]);
            }
        }
    }
}
}