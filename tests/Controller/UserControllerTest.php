<?php

    namespace Alambagaskara\LoginManagement\Controller;

        require_once __DIR__ . "/../Helper/helper.php";

        use Alambagaskara\LoginManagement\Config\Database;
        use Alambagaskara\LoginManagement\Domain\Session;
        use Alambagaskara\LoginManagement\Domain\User;
        use Alambagaskara\LoginManagement\Repository\SessionRepository;
        use Alambagaskara\LoginManagement\Repository\UserRepository;
        use Alambagaskara\LoginManagement\Service\SessionService;
        use PHPUnit\Framework\TestCase;
    
        class UserControllerTest extends TestCase {
    
            private UserController $userController;
            private UserRepository $userRepository;
            private SessionRepository $sessionRepository;
    
            public function setUp(): void {
    
                $this->userController = new UserController();
                $this->userRepository = new UserRepository(Database::getConnection());
                $this->sessionRepository = new SessionRepository(Database::getConnection());
    
                $this->sessionRepository->deleteAll();
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

            public function testLogin() {
                $this->userController->login();

                $this->expectOutputRegex("[Login User]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Password]");
            }

            public function testLoginSuccess() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
    
                $this->userRepository->save($user);

                $_POST['id'] = "Alam";
                $_POST['password'] = "Restu";

                $this->userController->postLogin();

                $this->expectOutputRegex("[Location: /]");
            }

            public function testLoginVaidationError() {
                $_POST['id'] = "";
                $_POST['password'] = "";

                $this->userController->postLogin();

                $this->expectOutputRegex("[Login User]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id atau Password Tidak Boleh Kosong]");
            }

            public function testLoginUserNotFound() {
                $_POST['id'] = "Lala";
                $_POST['password'] = "HAHA";

                $this->userController->postLogin();

                $this->expectOutputRegex("[Login User]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id atau Password salah]");
            }

            public function testLoginWrongPassword() {

                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
    
                $this->userRepository->save($user);

                $_POST['id'] = "Alam";
                $_POST['password'] = "HAHA";

                $this->userController->postLogin();

                $this->expectOutputRegex("[Login User]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id atau Password salah]");
            }

            public function testLogout() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $this->userController->logout();

                $this->expectOutputRegex("[X-ALM-SESSION: ]");
                $this->expectOutputRegex("[Location: /]");
            }

            public function testUpdateProfile() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $this->userController->updateProfile();

                $this->expectOutputRegex("[Profile]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Alam]");
                $this->expectOutputRegex("[Name]");
                $this->expectOutputRegex("[Bagas]");
            }

            public function testPostUpdateProfileSuccess() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $_POST['name'] = "Kara";
                $this->userController->postUpdateProfile();

                $this->expectOutputRegex("[Location: /]");

                $result = $this->userRepository->findById("Alam");
                self::assertEquals("Kara", $result->name);
            }

            public function testPostUpdateProfileValidationError() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $_POST['name'] = "";
                $this->userController->postUpdateProfile();

                $this->expectOutputRegex("[Profile]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Alam]");
                $this->expectOutputRegex("[Name]");
                $this->expectOutputRegex("[Id atau Name Tidak Boleh Kosong]");
            }

            public function testUpdatePassword() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $this->userController->updatePassword();

                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Alam]");

            }

            public function testPostUpdatePasswordSuccess() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $_POST['oldPassword'] = 'Restu';
                $_POST['newPassword'] = 'alam';

                $this->userController->postUpdatePassword();

                $this->expectOutputRegex('[Location: /]');

                $result = $this->userRepository->findById($user->id);
                self::assertTrue(password_verify("alam", $result->password));
            }

            public function testPostUpdatePasswordValidationError() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $_POST['oldPassword'] = '';
                $_POST['newPassword'] = '';

                $this->userController->postUpdatePassword();

                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Alam]");
                $this->expectOutputRegex("[Id, Old Password, New Password Tidak Boleh Kosong]");
            }

            public function testPostUpdatePasswordSWrongOldPassword() {
                $user = new User();
                $user->id = "Alam";
                $user->name = "Bagas";
                $user->password = password_hash("Restu", PASSWORD_BCRYPT);
                $this->userRepository->save($user);

                $session = new Session();
                $session->id = uniqid();
                $session->userId = $user->id;
                $this->sessionRepository->save($session);

                $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

                $_POST['oldPassword'] = 'salah';
                $_POST['newPassword'] = 'alam';

                $this->userController->postUpdatePassword();

                $this->expectOutputRegex("[Password]");
                $this->expectOutputRegex("[Id]");
                $this->expectOutputRegex("[Alam]");
                $this->expectOutputRegex("[Old Password Salah]");
            }
    }