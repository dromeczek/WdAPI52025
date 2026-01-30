<?php

require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class HabitController extends AppController
{
    private $habitRepository;
public function __construct()
    {
        // Usuwamy parent::__construct();
        $this->habitRepository = new HabitRepository();
    }

    public function addHabit()
    {
        // 1. Sprawdź czy użytkownik jest zalogowany
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2. Jeśli GET - pokaż formularz
        if ($this->isGet()) {
            return $this->render('add-habit');
        }

        // 3. Jeśli POST - dodaj do bazy
        $name = $_POST['name'] ?? null;

        if (!$name) {
            return $this->render('add-habit', ['messages' => ['Nazwa nawyku jest wymagana!']]);
        }

        $this->habitRepository->addHabit($name, $_SESSION['user_id']);

        // 4. Przekieruj na dashboard po sukcesie
        header('Location: /dashboard');
    }
public function water(array $params)
{
    $id = (int)$params['id'];
    
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    
    $this->habitRepository->waterHabit($id);
    
    header('Location: /dashboard');
}

public function deleteHabit(int $habitId): void {
    $conn = $this->database->connect();
    try {
        $conn->beginTransaction(); // Start transakcji

        // Usuwamy historię podlewania
        $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = :id");
        $stmt->execute([':id' => $habitId]);

        // Usuwamy sam nawyk
        $stmt = $conn->prepare("DELETE FROM habits WHERE id = :id");
        $stmt->execute([':id' => $habitId]);

        $conn->commit(); // Zatwierdzenie zmian
    } catch (Exception $e) {
        $conn->rollBack(); // Cofnięcie zmian w razie błędu
        throw $e;
    }
}
public function delete(array $params)
{
    $id = (int)$params['id'];
    
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Sprawdzamy czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    // Wywołujemy usuwanie z transakcją
    $this->habitRepository->deleteHabit($id, $_SESSION['user_id']);
    
    // Po usunięciu wracamy na dashboard
    header('Location: /dashboard');
}

    private function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    
}