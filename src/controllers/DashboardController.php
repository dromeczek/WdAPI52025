<?php
require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class DashboardController extends AppController {
    private $habitRepository;

    public function __construct() {
        $this->habitRepository = new HabitRepository();
    }

    public function dashboard() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        // KROK 1: Sprawdź czy roślinki nie usychają
        $this->habitRepository->updateHealthStatus($userId);

        // KROK 2: Pobierz aktualne dane
        $habits = $this->habitRepository->getHabits($userId);
        
        $this->render('dashboard', ['habits' => $habits]);
    }
}