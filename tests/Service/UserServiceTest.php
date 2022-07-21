<?php 

    namespace Alambagaskara\LoginManagement\Service;

    use Alambagaskara\LoginManagement\Config\Database;
use Alambagaskara\LoginManagement\Domain\User;
use Alambagaskara\LoginManagement\Exception\ValidationException;
    use Alambagaskara\LoginManagement\Model\UserRegisterRequest;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserServiceTest extends TestCase {

        private UserService $userService;
        private UserRepository $userRepository;

        protected function setUp(): void {

            $connection = Database::getConnection();
            $this->userRepository = new UserRepository($connection);
            $this->userService = new UserService($this->userRepository);

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
    }