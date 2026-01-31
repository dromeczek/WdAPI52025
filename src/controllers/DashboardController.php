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
        $roleId = $_SESSION['role_id'] ?? null; // Pobieramy role_id z sesji

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $this->habitRepository->updateHealthStatus($userId);
        $habits = $this->habitRepository->getHabits($userId);
        
        // Przekazujemy roleId do widoku
        $this->render('dashboard', [
            'habits' => $habits,
            'roleId' => $roleId 
        ]);
    }
}