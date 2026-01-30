<?php
// src/controllers/SecurityController.php

require_once __DIR__ . '/../../repository/UserRepository.php';

class SecurityController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../../public/views/login.html';
    }

    public function showRegister(): void
    {
        require __DIR__ . '/../../public/views/register.html';
    }

    public function handleRegister(): void
    {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;
        $email = $_POST['email'] ?? null;

        if (!$login || !$pass || !$email) {
            http_response_code(400);
            echo "Brak wymaganych danych do rejestracji.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo "Niepoprawny adres e-mail.";
            return;
        }

        if ($this->userRepository->findByLogin($login)) {
            http_response_code(400);
            echo "Użytkownik o takim loginie już istnieje.";
            return;
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        try {
            // Rejestracja użytkownika (domyślnie z rolą USER w bazie)
            $this->userRepository->createUser($login, $email, $hash);
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            return;
        }

        header('Location: /login');
        exit;
    }

    public function handleLogin(): void
    {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;

        if (!$login || !$pass) {
            http_response_code(400);
            echo "Brak loginu lub hasła.";
            return;
        }

        $user = $this->userRepository->findByLogin($login);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            http_response_code(401);
            echo "Nieprawidłowy login lub hasło.";
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);

        // ZAPISYWANIE DANYCH DO SESJI
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login']   = $user['login'];

        // OBSŁUGA ROLI (Twoje role_id dla admina to 2)
        if (isset($user['role_id']) && $user['role_id'] == 2) {
            $_SESSION['role'] = 'ADMIN';
        } else {
            $_SESSION['role'] = 'USER';
        }

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