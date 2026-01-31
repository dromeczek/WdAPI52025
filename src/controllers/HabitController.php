<?php

require_once 'AppController.php';
require_once __DIR__ . '/../../repository/HabitRepository.php';

class HabitController extends AppController {
    private $habitRepository;

    public function __construct() {
        $this->habitRepository = new HabitRepository();
    }

    public function addHabit() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Zmieniono z $this->isPost() na standardowe sprawdzenie metody żądania
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $targetDays = (int)$_POST['target_days'];
            $userId = $_SESSION['user_id'] ?? null;

            if (!$userId) {
                return $this->render('login', ['messages' => ['Sesja wygasła, zaloguj się ponownie.']]);
            }

            $this->habitRepository->addHabit($name, $targetDays, $userId);
            header('Location: /dashboard');
            exit;
        }

        return $this->render('add-habit');
    }

    public function water(array $params) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $habitId = (int)$params['id'];
        $todayCount = $this->habitRepository->getWateringCountToday($habitId);

        if ($todayCount > 0) {
            http_response_code(400);
            echo "Ta roślina była już dzisiaj podlewana! 🌱";
            return;
        }

        try {
            $this->habitRepository->waterHabit($habitId);
            http_response_code(200);
            echo "Sukces! Roślina odżyła.";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Błąd bazy danych.";
        }
    }

    public function delete(array $params) {
        $habitId = (int)$params['id'];
        $this->habitRepository->deleteHabit($habitId);
        header('Location: /dashboard');
    }
    public function waterAll() {
    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        $this->habitRepository->waterAll($userId);
        header('Location: /dashboard');
        exit;
    }
}
}