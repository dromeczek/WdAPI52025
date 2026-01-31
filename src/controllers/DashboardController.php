<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../../repository/HabitRepository.php';

class DashboardController extends AppController
{
    private HabitRepository $habitRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
    }

    // działa i przy routerze który woła bez parametrów, i przy routerze który przekaże tablicę
    public function index(array $params = []): void
    {
        // jeśli masz już mechanizm sesji / requireLogin w AppController, to tu go użyj
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $roleId = (int)($_SESSION['role_id'] ?? 1);

        // opcjonalnie: aktualizuj "więdnięcie" przed pobraniem
        $this->habitRepository->updateHealthStatus($userId);

        // ✅ to jest Twoja prawdziwa metoda w HabitRepository
        $habits = $this->habitRepository->getHabits($userId);

        $this->render('dashboard', [
            'habits' => $habits,
            'roleId' => $roleId,
        ]);
    }
}
