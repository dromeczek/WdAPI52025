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
    public function userDetails(array $params): void
{
    $this->requireAdmin();

    $userId = (int)($params['id'] ?? 0);
    if ($userId <= 0) {
        http_response_code(400);
        exit('Błędne ID użytkownika');
    }

    // dane z VIEW (to jest Twój cel)
    $userStats = $this->userRepository->getUserPlantStatsFromView($userId);

    if (!$userStats) {
        http_response_code(404);
        exit('Nie znaleziono użytkownika');
    }

    // opcjonalnie: dołóż listę nawyków usera
    $habits = $this->habitRepository->getHabits($userId);

    $this->render('admin-user-details', [
        'userStats' => $userStats,
        'habits' => $habits
    ]);
}

}
