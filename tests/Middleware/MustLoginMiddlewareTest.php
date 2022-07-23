<?php
    
    namespace Alambagaskara\LoginManagement\Middleware;

        require_once __DIR__ . "/../Helper/helper.php";

        use Alambagaskara\LoginManagement\Config\Database;
        use Alambagaskara\LoginManagement\Domain\Session;
        use Alambagaskara\LoginManagement\Domain\User;
        use Alambagaskara\LoginManagement\Repository\SessionRepository;
        use Alambagaskara\LoginManagement\Repository\UserRepository;
        use Alambagaskara\LoginManagement\Service\SessionService;
        use PHPUnit\Framework\TestCase;
    
        class MustLoginMiddlewareTest extends TestCase {
    
            private MustLoginMiddleware $middleware;
            private UserRepository $userRepository;
            private SessionRepository $sessionRepository;
    
            protected function setUp(): void {
                $this->middleware = new MustLoginMiddleware();
                $this->userRepository = new UserRepository(Database::getConnection());
                $this->sessionRepository = new SessionRepository(Database::getConnection());

                $this->sessionRepository->deleteAll();
                $this->userRepository->deleteAll();


                putenv("mode=test");
            }
    
            public function testBeforeGuest() {
                $this->middleware->before();
    
                $this->expectOutputRegex("[Location: /users/login]");
            }

            public function testBeforeLoginUser() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "alam";
                $user->password = "rahasia";
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $this->middleware->before();
    
                $this->expectOutputString("");
            }
        }