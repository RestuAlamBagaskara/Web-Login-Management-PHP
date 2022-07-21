<?php


    namespace Alambagaskara\LoginManagement\App {
        function header(string $value){
            echo $value;
        }
    }

    namespace Alambagaskara\LoginManagement\Controller {
        use Alambagaskara\LoginManagement\Config\Database;
        use Alambagaskara\LoginManagement\Domain\User;
        use Alambagaskara\LoginManagement\Repository\UserRepository;
        use PHPUnit\Framework\TestCase;
    
        class UserControllerTest extends TestCase {
    
            private UserController $userController;
            private UserRepository $userRepository;
    
            public function setUp(): void {
    
                $this->userController = new UserController();
                $this->userRepository = new UserRepository(Database::getConnection());
    
                $this->userRepository->deleteAll();

                putenv("mode=test");
            }
    
            public function testRegister() {
                $this->userController->register(); 
    
                $this->expectOutputRegex("[Register]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Name]");
                $this->expectOutputRegex("[Password]");
            }
    
            public function testPostRegisterSuccess() {
                $_POST['id'] = "Alam";
                $_POST['name'] = "Bagas";
                $_POST['password'] = "Restu";
    
                $this->userController->postRegister(); 
    
                $this->expectOutputRegex("[Location: /users/login]");
            }
    
            public function testPostRegisterValidationError() {
                $_POST['id'] = "";
                $_POST['name'] = "Bagas";
                $_POST['password'] = "Restu";
    
                $this->userController->postRegister(); 
    
                $this->expectOutputRegex("[Register]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Name]");
                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id, Name, Password Tidak Boleh Kosong]");
    
            }
    
            public function testPostRegisterDuplicate() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = "Restu";
    
                $this->userRepository->save($user);
    
                $_POST['id'] = "Alam";
                $_POST['name'] = "Bagas";
                $_POST['password'] = "Restu";
    
                $this->userController->postRegister(); 
    
                $this->expectOutputRegex("[Register]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Name]");
                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[User Sudah Ada]");
            }
    }

    }