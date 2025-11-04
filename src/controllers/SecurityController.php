<?php
// src/controllers/SecurityController.php
class SecurityController {
    public function showLogin(): void {
        require __DIR__ . '/../../public/views/login.html';
    }
    public function handleLogin(): void {
        // tu kiedyś walidacja; na razie pokaż dashboard
        header('Location: /dashboard');
        exit;
    }
    public function logout(): void {
        header('Location: /login');
        exit;
    }
}
