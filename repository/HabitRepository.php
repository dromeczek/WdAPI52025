<?php
require_once __DIR__ . '/Repository.php';

class HabitRepository extends Repository {

    public function addHabit(string $name, int $targetDays, string $reminderTime, int $userId): void {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO habits (name, target_days_per_week, reminder_time, user_id, current_health, created_at)
            VALUES (:name, :target_days, :reminder_time, :user_id, 100, NOW())
        ');

        $stmt->execute([
            ':name' => $name,
            ':target_days' => $targetDays,
            ':reminder_time' => $reminderTime,
            ':user_id' => $userId
        ]);
    }

  public function updateHealthStatus(int $userId): void {
    $stmt = $this->database->connect()->prepare("
        UPDATE habits h
        SET current_health = GREATEST(0, 100 - (
            EXTRACT(DAY FROM (NOW() - COALESCE(
                (SELECT MAX(date) FROM habit_logs hl WHERE hl.habit_id = h.id), 
                h.created_at
            )))::int * -- DYNAMICZNY MNOŻNIK KARY --
            CASE 
                WHEN h.target_days_per_week >= 7 THEN 15  -- Codziennie: surowa kara
                WHEN h.target_days_per_week >= 3 THEN 10  -- 3-5 dni: średnia kara
                ELSE 5                                    -- 1 dzień: mała kara
            END
        ))
        WHERE h.user_id = :userId
    ");
    $stmt->execute([':userId' => $userId]);
}

    public function waterHabit(int $habitId): void {
        $conn = $this->database->connect();
        
        // 1. Dodaj log podlania
        $stmt = $conn->prepare('
            INSERT INTO habit_logs (habit_id, was_watered, date)
            VALUES (?, true, NOW())
        ');
        $stmt->execute([$habitId]);

        // 2. Ulecz roślinkę do 100%
        $stmt = $conn->prepare('
            UPDATE habits SET current_health = 100 WHERE id = ?
        ');
        $stmt->execute([$habitId]);
    }

    public function getHabits(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM habits WHERE user_id = :userId ORDER BY created_at DESC
        ');
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteHabit(int $habitId, int $userId): void
{
    $conn = $this->database->connect();

    // 1. Najpierw usuwamy logi podlewania (klucze obce), aby baza nie wywaliła błędu
    $stmtLogs = $conn->prepare('
        DELETE FROM habit_logs WHERE habit_id = :habitId
    ');
    $stmtLogs->execute([':habitId' => $habitId]);

    // 2. Następnie usuwamy sam nawyk, upewniając się, że należy do zalogowanego użytkownika
    $stmtHabit = $conn->prepare('
        DELETE FROM habits WHERE id = :habitId AND user_id = :userId
    ');
    
    $stmtHabit->execute([
        ':habitId' => $habitId,
        ':userId' => $userId
    ]);
}
}