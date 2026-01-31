<?php

require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class HabitController extends AppController {
    private $habitRepository;

    public function __construct() {
        // Jeśli AppController nie ma konstruktora, nie wywołujemy parent::__construct()
        $this->habitRepository = new HabitRepository();
    }

    public function water(array $params) {
        $id = (int)$params['id'];
        $this->habitRepository->waterHabit($id);
        
        // Zwracamy kod sukcesu dla Fetch API zamiast przeładowania
        http_response_code(200);
        exit;
    }

    public function delete(array $params) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $id = (int)$params['id'];
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $this->habitRepository->deleteHabit($id, $userId);
        }
        
        header('Location: /dashboard');
        exit;
    }

    public function addHabit()
    {
        // Sprawdzamy sesję na początku metody
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Zastąpienie isPost() standardowym sprawdzeniem PHP
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? null;
            $targetDays = isset($_POST['target_days_per_week']) ? (int)$_POST['target_days_per_week'] : 7;
            $reminderTime = $_POST['reminder_time'] ?? '08:00';
            $userId = $_SESSION['user_id'] ?? null;

            if ($name && $userId) {
                $this->habitRepository->addHabit($name, $targetDays, $reminderTime, $userId);
                header("Location: /dashboard");
                exit;
            } else {
                // Jeśli brakuje danych, renderujemy ponownie z komunikatem
                return $this->render('add-habit', ['messages' => ['Błąd: Nie udało się dodać nawyku.']]);
            }
        }

        // Domyślnie pokazujemy formularz (dla żądania GET)
        return $this->render('add-habit');
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