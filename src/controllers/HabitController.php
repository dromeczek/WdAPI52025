<?php
require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class HabitController extends AppController {
    private $habitRepository;

    public function __construct() {
        $this->habitRepository = new HabitRepository();
    }

    public function water(array $params) {
        $id = (int)$params['id'];
        $this->habitRepository->waterHabit($id);
        
        // Zwracamy kod sukcesu dla Fetch API zamiast przeładowania
        http_response_code(200);
        exit;
    }

    public function delete(array $params) {
        $id = (int)$params['id'];
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $this->habitRepository->deleteHabit($id, $_SESSION['user_id']);
        header('Location: /dashboard');
    }

    public function addHabit() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->render('add-habit');
        }

        $name = $_POST['name'] ?? null;
        if ($name) {
            $this->habitRepository->addHabit($name, $_SESSION['user_id']);
            header('Location: /dashboard');
        }
    }
}