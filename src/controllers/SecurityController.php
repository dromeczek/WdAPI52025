<?php
// src/controllers/SecurityController.php

require_once __DIR__ . '/../../repository/UserRepository.php';

class SecurityController {

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function showLogin(): void {
        require __DIR__ . '/../../public/views/login.html';
    }

    public function showRegister(): void {
        require __DIR__ . '/../../public/views/register.html';
    }

    public function handleRegister(): void {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;
        $email = $_POST['email'] ?? null;

        if (!$login || !$pass || !$email) {
            http_response_code(400);
            echo "Brak wymaganych danych do rejestracji.";
            return;
        }

        // sprawdzenie czy login już istnieje
        if ($this->userRepository->findByLogin($login)) {
            http_response_code(400);
            echo "Użytkownik o takim loginie już istnieje.";
            return;
        }

        // zapis do bazy z hashowaniem (robi to createUser)
        $ok = $this->userRepository->createUser($login, $pass, $email);

        if (!$ok) {
            http_response_code(500);
            echo "Błąd podczas zapisu użytkownika.";
            return;
        }

        // po udanej rejestracji redirect na login
        header('Location: /login');
        exit;
    }

    public function handleLogin(): void {
        $login = $_POST['login'] ?? null;
        $pass  = $_POST['haslo'] ?? null;

        if (!$login || !$pass) {
            http_response_code(400);
            echo "Brak loginu lub hasła.";
            return;
        }

        // pobierz użytkownika po loginie
        $user = $this->userRepository->findByLogin($login);

        // jeśli nie ma użytkownika ALBO hasło nie pasuje → błąd
        if (!$user || !password_verify($pass, $user['password'])) {
            http_response_code(401);
            echo "Nieprawidłowy login lub hasło.";
            return;
        }

        // logujemy użytkownika – zapis do sesji
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // uwaga: w bazie masz kolumnę ID (wielkie litery)
        $_SESSION['user_id'] = $user['ID'] ?? null;
        $_SESSION['login']   = $user['login'] ?? null;

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // czyścimy sesję
        $_SESSION = [];
        session_destroy();

        header('Location: /login');
        exit;
    }
}
