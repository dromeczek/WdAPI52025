<?php

require_once 'AppController.php';
require_once __DIR__ . '/../../repository/HabitRepository.php';

class DashboardController extends AppController
{
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

  $habitRepository = new HabitRepository();
    
    // NAJPIERW: przelicz więdnięcie
    $habitRepository->refreshHabitsHealth($_SESSION['user_id']);
    
    // POTEM: pobierz świeże dane
    $habits = $habitRepository->getHabits($_SESSION['user_id']);

    $this->render('dashboard', ['habits' => $habits]);
    }
}