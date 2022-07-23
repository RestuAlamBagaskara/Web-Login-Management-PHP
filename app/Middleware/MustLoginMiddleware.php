<?php

    namespace Alambagaskara\LoginManagement\Middleware;

    use Alambagaskara\LoginManagement\App\View;
    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Repository\SessionRepository;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use Alambagaskara\LoginManagement\Service\SessionService;

    class MustLoginMiddleware implements Middleware {
        
        private SessionService $sessionService;

        public function __construct()
        {
            $userRepository = new UserRepository(Database::getConnection());
            $sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionService = new SessionService($sessionRepository ,$userRepository);
        }

        function before(): void
        {
            $user = $this->sessionService->current();
            if($user == null) {
                View::redirect("/users/login");
            }

        }
    }