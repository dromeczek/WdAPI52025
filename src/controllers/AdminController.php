<?php
require_once __DIR__ . '/../../repository/UserRepository.php';
require_once __DIR__ . '/../../repository/HabitRepository.php';
require_once __DIR__ . '/AppController.php';

class AdminController extends AppController
{
    private HabitRepository $habitRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->habitRepository = new HabitRepository();
        $this->userRepository = new UserRepository();
    }

    // GET /admin
    public function adminPanel(): void
    {
        $this->requireAdmin();

        $users = $this->userRepository->getUsersWithHabits();
        $this->render('admin-panel', ['users' => $users]);
    }

    // POST /ban/{id}
    public function ban(array $params): void
    {
        $this->requireAdmin();

        $id = (int)($params['id'] ?? 0);
        if ($id > 0) {
            $this->userRepository->banUser($id);
        }

        $this->redirect('/admin');
    }

    // POST /unban/{id}
    public function unban(array $params): void
    {
        $this->requireAdmin();

        $id = (int)($params['id'] ?? 0);
        if ($id > 0) {
            $this->userRepository->unbanUser($id);
        }

        $this->redirect('/admin');
    }
}
