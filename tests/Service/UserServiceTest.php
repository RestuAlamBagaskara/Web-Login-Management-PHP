<?php 

    namespace Alambagaskara\LoginManagement\Service;

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\User;
    use Alambagaskara\LoginManagement\Exception\ValidationException;
    use Alambagaskara\LoginManagement\Model\UserLoginRequest;
    use Alambagaskara\LoginManagement\Model\UserRegisterRequest;
    use Alambagaskara\LoginManagement\Model\UserUpdatePasswordRequest;
    use Alambagaskara\LoginManagement\Model\UserUpdateProfileRequest;
    use Alambagaskara\LoginManagement\Repository\SessionRepository;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserServiceTest extends TestCase {

        private UserService $userService;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void {

            $connection = Database::getConnection();
            $this->userRepository = new UserRepository($connection);
            $this->sessionRepository = new SessionRepository($connection);
            $this->userService = new UserService($this->userRepository);

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testRegisterSuccess() {
            
            $request = new UserRegisterRequest();
            $request->id = "Alam";
            $request->name = "Bagas";
            $request->password = "Restu";
            $response = $this->userService->register($request);

            self::assertEquals($request->id, $response->user->id);
            self::assertEquals($request->name, $response->user->name);
            self::assertNotEquals($request->password, $response->user->password);

            self::assertTrue(password_verify($request->password, $response->user->password));
        }
        
        public function testRegisterFailed() {
            $this->expectException(ValidationException::class);

            $request = new UserRegisterRequest();
            $request->id = "";
            $request->name = "";
            $request->password = "";
            $this->userService->register($request);
        }

        public function testRegisterDuplicate() {
            $user = new User();
            $user->id = "Alam";
            $user->name = "bagas";
            $user->password = "Restu";

            $this->userRepository->save($user); 

            $this->expectException(ValidationException::class);

            $request = new UserRegisterRequest();
            $request->id = "Alam";
            $request->name = "bagas";
            $request->password = "Restu";

            $this->userService->register($request);
        }

        public function testLoginNotFound() {
            
            $this->expectException(ValidationException::class);

            $request = new UserLoginRequest();
            $request->id = 'Alam';
            $request->password = 'Bagas';

            $this->userService->login($request);
        }

        public function testLoginWrongPassword() {

            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = password_hash("Restu", PASSWORD_BCRYPT);

            $this->expectException(ValidationException::class);
            
            $request = new UserLoginRequest();
            $request->id = 'Alam';
            $request->password = 'Bagas';

            $this->userService->login($request);
        }

        public function testLoginSuccess() {
            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = password_hash("Restu", PASSWORD_BCRYPT);

            $this->expectException(ValidationException::class);
            
            $request = new UserLoginRequest();
            $request->id = 'Alam';
            $request->password = 'Restu';

            $response = $this->userService->login($request);

            self::assertEquals($request->id, $response->user->id);
            self::assertTrue(password_verify($request->password, $response->user->password));
        }

        public function testUpdateSuccess() {
            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = password_hash("Restu", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            
            $request = new UserUpdateProfileRequest();
            $request->id = "Alam";
            $request->name = "Restu";

            $this->userService->updateProfile($request);

            $result = $this->userRepository->findById($user->id);
            self::assertEquals($request->name, $result->name);
        }

        public function testUpdateValidationError() {
            $this->expectException(ValidationException::class);

            $request = new UserUpdateProfileRequest();
            $request->id = "";
            $request->name = "";

            $this->userService->updateProfile($request);
        }

        public function testUpdateNotFound() {
            $this->expectException(ValidationException::class);

            $request = new UserUpdateProfileRequest();
            $request->id = "Alam";
            $request->name = "Restu";

            $this->userService->updateProfile($request);

        }

        public function testUpdatePasswordSuccess() {
            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = password_hash("Restu", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $request = new UserUpdatePasswordRequest();
            $request->id = "Alam";
            $request->oldPassword = "Restu";
            $request->newPassword = "Alam";

            $this->userService->updatePassword($request);

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify($request->newPassword, $result->password));
        }

        public function testUpdatePasswordValidationError() {
            $this->expectException(ValidationException::class);

            $request = new UserUpdatePasswordRequest();
            $request->id = "Alam";
            $request->oldPassword = "";
            $request->newPassword = "";

            $this->userService->updatePassword($request);
        }

        public function testUpdatePasswordWrongOldPassword() {
            $this->expectException(ValidationException::class);

            $user = new User();
            $user->id = "Alam";
            $user->name = "Bagas";
            $user->password = password_hash("Restu", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $request = new UserUpdatePasswordRequest();
            $request->id = "Alam";
            $request->oldPassword = "salah";
            $request->newPassword = "Alam";

            $this->userService->updatePassword($request);
        }

        public function testUpdatePasswordNotFound() {
            $this->expectException(ValidationException::class);

            $request = new UserUpdatePasswordRequest();
            $request->id = "Alam";
            $request->oldPassword = "salah";
            $request->newPassword = "Alam";

            $this->userService->updatePassword($request);
        }
    }