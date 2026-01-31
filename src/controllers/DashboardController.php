<?php
require_once 'AppController.php'; // Jeśli jest w tym samym folderze
require_once __DIR__ . '/../../repository/HabitRepository.php'; // Ścieżka do repozytorium
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

        // 1. Aktualizacja zdrowia (logika więdnięcia)
        $this->habitRepository->updateHealthStatus($userId);

        // 2. Pobranie nawyków wraz z nowymi statystykami (podlania)
        $habits = $this->habitRepository->getHabits($userId);
        
        $this->render('dashboard', [
            'habits' => $habits
        ]);
    }
}