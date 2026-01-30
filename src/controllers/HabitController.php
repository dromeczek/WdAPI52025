<?php

require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';

class HabitController extends AppController
{
    private $habitRepository;
public function __construct()
    {
        // Usuwamy parent::__construct();
        $this->habitRepository = new HabitRepository();
    }

    public function addHabit()
    {
        // 1. Sprawdź czy użytkownik jest zalogowany
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2. Jeśli GET - pokaż formularz
        if ($this->isGet()) {
            return $this->render('add-habit');
        }

        // 3. Jeśli POST - dodaj do bazy
        $name = $_POST['name'] ?? null;

        if (!$name) {
            return $this->render('add-habit', ['messages' => ['Nazwa nawyku jest wymagana!']]);
        }

        $this->habitRepository->addHabit($name, $_SESSION['user_id']);

        // 4. Przekieruj na dashboard po sukcesie
        header('Location: /dashboard');
    }

    private function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}