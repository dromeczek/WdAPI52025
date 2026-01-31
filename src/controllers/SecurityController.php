<?php

require_once __DIR__ . '/../../repository/UserRepository.php';

class SecurityController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function handleLogin(): void
    {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;

        if (!$login || !$pass) {
            header('Location: /login?error=missing_data');
            exit;
        }

        $user = $this->userRepository->findByLogin($login);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            header('Location: /login?error=bad_credentials');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);

        // ZAPISYWANIE DANYCH DO SESJI
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login']   = $user['login'];
        $_SESSION['role_id'] = $user['role_id']; // To jest kluczowe dla admina!

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
        exit;
    }
}