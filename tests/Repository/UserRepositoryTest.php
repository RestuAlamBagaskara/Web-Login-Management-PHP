<?php

    namespace Alambagaskara\LoginManagement\Repository;

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\User;
    use PHPUnit\Framework\TestCase;

    class UserRepositoryTest extends TestCase {

        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void {
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testSeveSuccess() {
            $user = new User;
            $user->id = 'Alam';
            $user->name = 'Bagas';
            $user->password = 'Restu';

            $this->userRepository->save($user);

            $result = $this->userRepository->findById($user->id);

            self::assertEquals($user->id, $result->id);
            self::assertEquals($user->name, $result->name);
            self::assertEquals($user->password, $result->password);
        }

        public function testFindByIdNotFound() {
            $user = $this->userRepository->findById("notFound");
            self::assertNull($user);
        }

        public function testUpdate() {
            $user = new User;
            $user->id = 'Alam';
            $user->name = 'Bagas';
            $user->password = 'Restu';
            $this->userRepository->save($user);

            $user->name = "Kara";
            $this->userRepository->update($user);

            $result = $this->userRepository->findById($user->id);

            self::assertEquals($user->id, $result->id);
            self::assertEquals($user->name, $result->name);
            self::assertEquals($user->password, $result->password);
        }
    }