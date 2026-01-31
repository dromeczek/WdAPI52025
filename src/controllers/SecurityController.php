<?php

require_once 'AppController.php';
require_once __DIR__ . '/../../repository/UserRepository.php';

class SecurityController extends AppController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // TA METODA NAPRAWIA BŁĄD W index.php:23
    public function showLogin(): void
    {
        $this->render('login');
    }

    public function handleLogin(): void
    {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;

        $user = $this->userRepository->findByLogin($login);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            $this->render('login', ['messages' => ['Nieprawidłowe dane!']]);
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        
        // Ustawiamy role dla AdminController
        $_SESSION['role'] = ((int)$user['role_id'] === 2) ? 'ADMIN' : 'USER';

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}