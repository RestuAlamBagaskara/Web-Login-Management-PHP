<?php

    namespace Alambagaskara\LoginManagement\Controller;

use Alambagaskara\LoginManagement\Config\Database;
use Alambagaskara\LoginManagement\Domain\Session;
use Alambagaskara\LoginManagement\Domain\User;
use Alambagaskara\LoginManagement\Repository\SessionRepository;
use Alambagaskara\LoginManagement\Repository\UserRepository;
use Alambagaskara\LoginManagement\Service\SessionService;
use PhpParser\Node\Expr\New_;
use PHPUnit\Framework\TestCase;

    class HomeControllerTest extends TestCase {

        private HomeController $homeController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void {
            $this->homeController = new HomeController();
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->userRepository = new UserRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

        }

        public function testGuest(){
            $this->homeController->index();

            $this->expectOutputRegex("[Login Management]");
        }

        public function testUserLogin(){
            $user = new User();
            $user->id = "alam";
            $user->name = "Alam";
            $user->password = "rahasia";
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->homeController->index();

            $this->expectOutputRegex("[Hello, Alam]");
        }
    }