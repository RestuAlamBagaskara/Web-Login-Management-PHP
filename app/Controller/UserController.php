<?php

    namespace Alambagaskara\LoginManagement\Controller;

    use Alambagaskara\LoginManagement\App\View;
    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Exception\ValidationException;
    use Alambagaskara\LoginManagement\Model\UserRegisterRequest;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use Alambagaskara\LoginManagement\Service\UserService;

    class UserController {

        private UserService $userService;

        public function __construct() {
            $connection = Database::getConnection();
            $userRepository = new UserRepository($connection);
            $this->userService = new UserService($userRepository); 
        }

        public function register() {
            View::render('User/register',[
                'title' => "Register User"
            ]);
        }

        public function postRegister() {
            $request = new UserRegisterRequest();

            $request->id = $_POST['id'];
            $request->name = $_POST['name'];
            $request->password = $_POST['password'];

            try {
                $this->userService->register($request);
                View::redirect('/users/login');
            } catch (ValidationException $exception) {
                View::render('User/register',[
                    'title' => "Register User",
                    'error' => $exception->getMessage()
                ]);
            }
        }
    }