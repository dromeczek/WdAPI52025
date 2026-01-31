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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? null;
            $targetDays = isset($_POST['target_days']) ? (int)$_POST['target_days'] : 7;
            $userId = $_SESSION['user_id'] ?? null;

            if ($name && $userId) {
                $this->habitRepository->addHabit($name, $targetDays, $userId);
                header('Location: /dashboard');
                exit;
            }
        }

        return $this->render('add-habit');
    }

    public function water(array $params) {
        $habitId = (int)$params['id'];
        if ($this->habitRepository->getWateringCountToday($habitId) > 0) {
            http_response_code(400);
            echo "Już podlane!";
            return;
        }
        $this->habitRepository->waterHabit($habitId);
        http_response_code(200);
    }

    public function waterAll() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->habitRepository->waterAll($userId);
            header('Location: /dashboard');
            exit;
        }
    }
}