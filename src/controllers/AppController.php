<?php

class AppController
{
    protected function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function redirect(string $to): void
    {
        header("Location: {$to}");
        exit;
    }

    protected function requireLogin(): void
    {
        $this->startSession();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireLogin();

        // U Ciebie na dashboardzie admin jest po roleId === 2
        if (!isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 2) {
            http_response_code(403);
            exit('Brak uprawnień');
        }
    }

    protected function render(string $template = null, array $variables = []): void
    {
        $this->startSession();
        $templatePath = 'public/views/' . $template . '.html';
        $templatePath404 = 'public/views/404.html';
        $output = "";

        if (file_exists($templatePath)) {
            extract($variables);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        } else {
            ob_start();
            include $templatePath404;
            $output = ob_get_clean();
        }

        echo $output;
    }
}
