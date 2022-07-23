<?php

    namespace Alambagaskara\LoginManagement\Controller;

    use Alambagaskara\LoginManagement\App\View;
    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Repository\SessionRepository;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use Alambagaskara\LoginManagement\Service\SessionService;

    class HomeController {

        private SessionService $sessionService;

        public function __construct()
        {
            $connection = Database::getConnection();
            $sessionRepository = new SessionRepository($connection);
            $userRepository = new UserRepository($connection);
            $this->sessionService = new SessionService($sessionRepository, $userRepository);
        }

        public function index(): void {
            $user = $this->sessionService->current();
            if ($user == null) {
                View::render('Home/index', [
                    "title" => "Web Login Management"
                ]);
            } else {
                View::render('Home/dashboard', [
                    "title" => "Dashboard",
                    "user" => [
                        "name" => $user->name
                    ]
                ]);
            }

        }
    }