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
public function deleteHabit(int $habitId, int $userId): void
{
    $conn = $this->database->connect();
    
    try {
        // ROZPOCZĘCIE TRANSAKCJI
        $conn->beginTransaction();

        // 1. Usuwamy logi (historię podlewania) danej rośliny
        $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = :id");
        $stmt->execute([':id' => $habitId]);

        // 2. Usuwamy samą roślinę, sprawdzając czy należy do zalogowanego użytkownika (bezpieczeństwo!)
        $stmt = $conn->prepare("DELETE FROM habits WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $habitId,
            ':user_id' => $userId
        ]);

        // ZATWIERDZENIE ZMIAN
        $conn->commit();
        
    } catch (Exception $e) {
        // W razie jakiegokolwiek błędu - cofamy wszystkie zmiany
        $conn->rollBack();
        throw $e;
    }
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
public function waterHabit(int $habitId): void
{
    $conn = $this->database->connect();
    
    try {
        // 1. Logujemy podlanie (żeby system więdnięcia wiedział, kiedy była ostatnia akcja)
        $stmt = $conn->prepare("
            INSERT INTO habit_logs (habit_id, date) 
            VALUES (:habit_id, CURRENT_DATE)
        ");
        $stmt->execute([':habit_id' => $habitId]);

        // 2. Zmieniamy logikę: zamiast LEAST(100, current_health + 10) dajemy po prostu 100
        $stmt = $conn->prepare("
            UPDATE habits 
            SET current_health = 100 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $habitId]);
        
    } catch (PDOException $e) {
        // Jeśli już podlano dzisiaj, UNIQUE constraint w bazie zablokuje ponowny INSERT do habit_logs.
        // Jeśli chcesz, aby mimo to zdrowie i tak skoczyło do 100, 
        // możesz wynieść UPDATE poza blok try-catch lub dodać go też w sekcji catch.
        
        $stmt = $conn->prepare("UPDATE habits SET current_health = 100 WHERE id = :id");
        $stmt->execute([':id' => $habitId]);
    }
}
}