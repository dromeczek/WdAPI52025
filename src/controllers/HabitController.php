<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../../repository/HabitRepository.php';

class HabitController extends AppController
{
    private HabitRepository $habitRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
    }

    private function ensureLoggedIn(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        return (int)$_SESSION['user_id'];
    }

    // GET /addHabit  (router oczekuje form())
    public function form(array $params = []): void
    {
        $this->ensureLoggedIn();
        $this->render('add-habit');
    }

    // POST /addHabit (router oczekuje add())
    public function add(array $params = []): void
    {
        $userId = $this->ensureLoggedIn();

        $name = trim($_POST['name'] ?? '');
        $targetDays = isset($_POST['target_days']) ? (int)$_POST['target_days'] : 7;

        if ($name !== '') {
            $this->habitRepository->addHabit($name, $targetDays, $userId);
        }

        header('Location: /dashboard');
        exit;
    }

    // Alias: zostawiam, bo mogłeś mieć gdzieś jeszcze routing/stare linki do addHabit()
    public function addHabit(array $params = []): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->add($params);
            return;
        }
        $this->form($params);
    }

    // GET /water/{id}
    public function water(array $params): void
    {
        $this->ensureLoggedIn();

        $habitId = (int)($params['id'] ?? 0);
        if ($habitId <= 0) {
            http_response_code(400);
            echo "Błędne ID";
            return;
        }

        if ($this->habitRepository->getWateringCountToday($habitId) > 0) {
            http_response_code(400);
            echo "Już podlane!";
            return;
        }

        $this->habitRepository->waterHabit($habitId);
        http_response_code(200);
        echo "OK";
    }

    // GET /waterAll
    public function waterAll(array $params = []): void
    {
        $userId = $this->ensureLoggedIn();

        $this->habitRepository->waterAll($userId);
        header('Location: /dashboard');
        exit;
    }
    public function delete(array $params = []): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $habitId = (int)($params['id'] ?? 0);
    $userId  = (int)$_SESSION['user_id'];

    if ($habitId > 0) {
        $this->habitRepository->deleteHabit($habitId, $userId);
    }

    header('Location: /dashboard');
    exit;
}

}
