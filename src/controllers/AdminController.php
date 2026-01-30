<?php
require_once 'AppController.php';
require_once __DIR__ .'/../../repository/HabitRepository.php';
require_once __DIR__ .'/../../repository/UserRepository.php';

class AdminController extends AppController {
    private $habitRepository;
    private $userRepository;

    public function __construct() {
        // Usunięto parent::__construct() - to był powód błędu
        $this->habitRepository = new HabitRepository();
        $this->userRepository = new UserRepository();
    }

    public function adminPanel() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $role = $_SESSION['role'] ?? null;
        if (!$role || $role !== 'ADMIN') {
            header('Location: /dashboard');
            exit;
        }

        $users = $this->userRepository->getUsersWithHabits();
        $this->render('admin-panel', ['users' => $users]);
    }

    public function ban($params) {
        $this->userRepository->banUser($params['id']);
        header("Location: /admin");
        exit();
    }
    public function unban($params) {
    $this->userRepository->unbanUser($params['id']);
    header("Location: /admin");
    exit();
}
}