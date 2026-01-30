<?php
require_once __DIR__ . '/Repository.php';

class HabitRepository extends Repository {
    
    public function getHabits(int $userId): array {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Metoda korzystająca z WIDOKU (View) w SQL
    public function getAllUsersStats(): array {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM v_user_plant_stats ORDER BY total_plants DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Usuwanie nawyku z użyciem TRANSAKCJI
    public function deleteHabit(int $habitId, int $userId): void {
        $conn = $this->database->connect();
        try {
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = :id");
            $stmt->execute([':id' => $habitId]);

            $stmt = $conn->prepare("DELETE FROM habits WHERE id = :id AND user_id = :u_id");
            $stmt->execute([':id' => $habitId, ':u_id' => $userId]);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function waterHabit(int $habitId): void {
        $conn = $this->database->connect();
        try {
            $stmt = $conn->prepare("INSERT INTO habit_logs (habit_id, date) VALUES (:id, CURRENT_DATE)");
            $stmt->execute([':id' => $habitId]);
            $stmt = $conn->prepare("UPDATE habits SET current_health = 100 WHERE id = :id");
            $stmt->execute([':id' => $habitId]);
        } catch (PDOException $e) {
            $stmt = $conn->prepare("UPDATE habits SET current_health = 100 WHERE id = :id");
            $stmt->execute([':id' => $habitId]);
        }
    }

    public function refreshHabitsHealth(int $userId): void {
        $conn = $this->database->connect();
        $habits = $this->getHabits($userId);
        foreach ($habits as $habit) {
            $stmt = $conn->prepare("SELECT MAX(date) FROM habit_logs WHERE habit_id = :id");
            $stmt->execute([':id' => $habit['id']]);
            $last = $stmt->fetchColumn();
            if ($last) {
                $days = (new DateTime())->diff(new DateTime($last))->days;
                if ($days > 0) {
                    $dmg = $days * 20;
                    $stmt = $conn->prepare("UPDATE habits SET current_health = GREATEST(0, current_health - :dmg) WHERE id = :id");
                    $stmt->execute([':dmg' => $dmg, ':id' => $habit['id']]);
                }
            }
        }
    }
    public function addHabit(string $name, int $targetDays, string $reminderTime, int $userId): void
{
    $stmt = $this->database->connect()->prepare('
        INSERT INTO habits (name, target_days_per_week, reminder_time, user_id, current_health)
        VALUES (?, ?, ?, ?, 100)
    ');

    $stmt->execute([
        $name,
        $targetDays,
        $reminderTime,
        $userId
    ]);
}
}