<?php
require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class AdminController extends AppController {
    private $habitRepository;

    public function __construct() {
        $this->habitRepository = new HabitRepository();
    }

    public function adminPanel() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Pobieramy rolę z sesji lub ustawiamy null jeśli nie istnieje
        $role = $_SESSION['role'] ?? null;

        // Sprawdzanie uprawnień - tylko ADMIN ma wstęp
        if (!$role || $role !== 'ADMIN') {
            header('Location: /dashboard');
            exit;
        }

        // Pobieramy dane z WIDOKU SQL (v_user_plant_stats)
        $stats = $this->habitRepository->getAllUsersStats();
        $this->render('admin-panel', ['stats' => $stats]);
    }
}