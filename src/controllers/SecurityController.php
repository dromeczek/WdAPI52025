<?php
require_once __DIR__ . '/../../repository/UserRepository.php';
require_once __DIR__ . '/AppController.php';

class SecurityController extends AppController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // GET /login
    public function loginForm(): void
    {
        $this->render('login');
    }

public function login(): void
{
    $login = trim($_POST['login'] ?? '');
    $password = (string)($_POST['haslo'] ?? '');

    if ($login === '' || $password === '') {
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => 'Podaj login i hasło.'], 422);
        }
        $this->render('login', ['error' => 'Podaj login i hasło.']);
        return;
    }

    $user = $this->userRepository->findByLogin($login);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => 'Nieprawidłowy login lub hasło.'], 401);
        }
        $this->render('login', ['error' => 'Nieprawidłowy login lub hasło.']);
        return;
    }

    // BAN / is_active = 0
    if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => 'Twoje konto jest zbanowane.'], 403);
        }
        $this->startSession();
        $_SESSION['flash_error'] = 'Twoje konto jest zbanowane. Skontaktuj się z administratorem.';
        $this->redirect('/login');
    }

    $this->startSession();
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['login'] = $user['login'];
    $_SESSION['role_id'] = (int)$user['role_id'];

    if ($this->isAjax()) {
        $this->json(['success' => true, 'redirect' => '/dashboard']);
    }

    $this->redirect('/dashboard');
}

    // GET /register
    public function registerForm(): void
    {
        $this->render('register');
    }

    // POST /register
   public function register(): void
{
    $login = trim($_POST['login'] ?? '');
    $password = (string)($_POST['haslo'] ?? '');

    if ($login === '' || $password === '') {
        $this->json([
            'success' => false,
            'message' => 'Login i hasło są wymagane'
        ], 422);
    }

    if (strlen($password) < 6) {
        $this->json([
            'success' => false,
            'message' => 'Hasło musi mieć min. 6 znaków'
        ], 422);
    }

    if ($this->userRepository->existsByLogin($login)) {
        $this->json([
            'success' => false,
            'message' => 'Użytkownik już istnieje'
        ], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $this->userRepository->create([
        'login' => $login,
        'password_hash' => $hash,
        'role_id' => 1 // USER
    ]);

    $this->json([
        'success' => true,
        'message' => 'Konto utworzone. Możesz się zalogować.',
        'redirect' => '/login'
    ], 201);
}


    // GET /logout
    public function logout(): void
    {
        $this->startSession();
        session_destroy();
        $this->redirect('/login');
    }
    public function root(array $params = []): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
}

}
