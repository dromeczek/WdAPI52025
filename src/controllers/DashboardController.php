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

        // 1. Tworzymy instancję repozytorium
        $habitRepository = new HabitRepository();
        
        // 2. Pobieramy nawyki zalogowanego użytkownika
        $habits = $habitRepository->getHabits($_SESSION['user_id']);

        // 3. Renderujemy widok, przekazując tablicę $habits
        // Metoda render sprawi, że w pliku .html zmienna $habits będzie dostępna
        $this->render('dashboard', ['habits' => $habits]);
    }
}