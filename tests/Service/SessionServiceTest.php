<?php

    namespace Alambagaskara\LoginManagement\Service;

    require_once __DIR__ . "/../Helper/helper.php";

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\Session;
    use Alambagaskara\LoginManagement\Domain\User;
    use Alambagaskara\LoginManagement\Repository\SessionRepository;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class SessionServiceTest extends TestCase {

        private SessionService $sessionService;
        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;

        protected function setUp(): void {
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->userRepository = new userRepository(Database::getConnection());
            $this->sessionService = new SessionService($this->sessionRepository ,$this->userRepository);    

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = "Restu";
            $this->userRepository->save($user);
        }

        public function testCreate() {
            $session = $this->sessionService->create("Alam");

            $this->expectOutputRegex("[X-ALM-SESSION : $session->id]");

            $result = $this->sessionRepository->findById($session->id);
            self::assertEquals("Alam", $result->userId);
        }

        public function testDestroy() {
            $session = new Session();

            $session->id = uniqid();
            $session->userId = "Alam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $this->sessionService->destroy();

            $this->expectOutputRegex("[X-ALM-SESSION :]");
            $result = $this->sessionRepository->findById($session->id);

            self::assertNull($result);
        }

        public function testCurrent() {
            $session = new Session();

            $session->id = uniqid();
            $session->userId = "Alam";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $user = $this->sessionService->current();

            self::assertEquals($session->userId, $user->id);
        }
    }   