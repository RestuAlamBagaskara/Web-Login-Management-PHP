<?php

    namespace Alambagaskara\LoginManagement\Service;

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\User;
    use Alambagaskara\LoginManagement\Exception\ValidationException;
    use Alambagaskara\LoginManagement\Model\UserRegisterRequest;
    use Alambagaskara\LoginManagement\Model\UserRegisterResponse;
    use Alambagaskara\LoginManagement\Repository\UserRepository;
    use Exception;

    class UserService {

        private UserRepository $userRepository;

        public function __construct(UserRepository $userRepository)
        {
            $this->userRepository = $userRepository;
        }

        public function register(UserRegisterRequest $request): UserRegisterResponse {
            $this->validateUserRegistrationRequest($request);

            try{
                Database::beginTransaction();

                $user = $this->userRepository->findById($request->id);
                if($user !== null) {
                    throw new ValidationException("User Sudah Ada");
                }
    
                $user = new User();
                $user->id = $request->id;
                $user->name = $request->name;
                $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
                $this->userRepository->save($user);
    
                $response = new UserRegisterResponse();
                $response->user = $user;
    
                Database::commitTransaction();
                return $response;
            }
            catch (Exception $exception){
                Database::rollbackTransaction();
                throw $exception;
            }

        }

        private function validateUserRegistrationRequest(UserRegisterRequest $request) {
            if($request->id == null || $request->name == null || $request->password == null || trim($request->id == "") || trim($request->name == "") || trim($request->password == "")) {
                throw new ValidationException("Id, Name, Password Tidak Boleh Kosong");
            }
        }
    }