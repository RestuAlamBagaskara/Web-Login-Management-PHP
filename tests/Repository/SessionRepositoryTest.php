<?php

    namespace Alambagaskara\LoginManagement\Repository;

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\Session;
    use Alambagaskara\LoginManagement\Domain\User;
    use PHPUnit\Framework\TestCase;

    class SessionRepositoryTest extends TestCase {

        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;

        protected function setUp(): void {
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->userRepository = new UserRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = "Restu";
            $this->userRepository->save($user);
        }

        public function testSaveSuccess() {
            $session = new Session();

            $session->id = uniqid();
            $session->userId = "alam";

            $this->sessionRepository->save($session);

            $result = $this->sessionRepository->findById($session->id);
            self::assertEquals($session->id, $result->id);
            self::assertEquals($session->userId, $result->userId);

        }

        public function testDeleteByIdSuccess() {
            $session = new Session();

            $session->id = uniqid();
            $session->userId = "alam";

            $this->sessionRepository->save($session);

            $result = $this->sessionRepository->findById($session->id);
            self::assertEquals($session->id, $result->id);
            self::assertEquals($session->userId, $result->userId);

            $this->sessionRepository->deleteById($session->id);

            $result = $this->sessionRepository->findById($session->id);
            self::assertNull($result);
        }

        public function testFindByIdNotFound() {
            $result = $this->sessionRepository->findById("TidakAda");
            self::assertNull($result);
        }

    }