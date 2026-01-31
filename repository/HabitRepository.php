<?php

require_once __DIR__ . '/Repository.php';

class HabitRepository extends Repository {

    public function getHabits(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT h.*, COUNT(hl.id) as water_count
            FROM habits h 
            LEFT JOIN habit_logs hl ON h.id = hl.habit_id 
            WHERE h.user_id = :userId 
            GROUP BY h.id 
            ORDER BY h.created_at DESC
        ');
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // TEJ METODY BRAKOWAŁO:
    public function addHabit(string $name, int $targetDays, int $userId): void {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO habits (name, target_days_per_week, user_id, current_health, created_at)
            VALUES (?, ?, ?, 100, NOW())
        ');
        $stmt->execute([$name, $targetDays, $userId]);
    }

    public function waterHabit(int $habitId): void {
        $conn = $this->database->connect();
        $stmt = $conn->prepare('INSERT INTO habit_logs (habit_id, was_watered, date) VALUES (?, true, NOW())');
        $stmt->execute([$habitId]);

        $stmt = $conn->prepare('UPDATE habits SET current_health = 100 WHERE id = ?');
        $stmt->execute([$habitId]);
    }

    public function waterAll(int $userId): void {
        $conn = $this->database->connect();
        $stmt = $conn->prepare('
            INSERT INTO habit_logs (habit_id, was_watered, date)
            SELECT id, true, NOW() FROM habits 
            WHERE user_id = ? AND id NOT IN (
                SELECT habit_id FROM habit_logs WHERE date::date = CURRENT_DATE
            )
        ');
        $stmt->execute([$userId]);

        $stmt = $conn->prepare('UPDATE habits SET current_health = 100 WHERE user_id = ?');
        $stmt->execute([$userId]);
    }

    public function getWateringCountToday(int $habitId): int {
        $stmt = $this->database->connect()->prepare('
            SELECT COUNT(*) FROM habit_logs 
            WHERE habit_id = ? AND date::date = CURRENT_DATE
        ');
        $stmt->execute([$habitId]);
        return (int)$stmt->fetchColumn();
    }

    public function updateHealthStatus(int $userId): void {
        $stmt = $this->database->connect()->prepare("
            UPDATE habits h
            SET current_health = GREATEST(0, 100 - (
                EXTRACT(DAY FROM (NOW() - COALESCE(
                    (SELECT MAX(date) FROM habit_logs hl WHERE hl.habit_id = h.id), 
                    h.created_at
                )))::int * 10
            ))
            WHERE h.user_id = :userId
        ");
        $stmt->execute([':userId' => $userId]);
    }
}