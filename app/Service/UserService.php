<?php

    namespace Alambagaskara\LoginManagement\Service;

    use Alambagaskara\LoginManagement\Config\Database;
    use Alambagaskara\LoginManagement\Domain\User;
    use Alambagaskara\LoginManagement\Exception\ValidationException;
use Alambagaskara\LoginManagement\Model\UserLoginRequest;
use Alambagaskara\LoginManagement\Model\UserLoginResponse;
use Alambagaskara\LoginManagement\Model\UserRegisterRequest;
    use Alambagaskara\LoginManagement\Model\UserRegisterResponse;
use Alambagaskara\LoginManagement\Model\UserUpdatePasswordRequest;
use Alambagaskara\LoginManagement\Model\UserUpdatePasswordResponse;
use Alambagaskara\LoginManagement\Model\UserUpdateProfileRequest;
use Alambagaskara\LoginManagement\Model\UserUpdateProfileReseponse;
use Alambagaskara\LoginManagement\Model\UserUpdateProfileResponse;
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

        public function login(UserLoginRequest $request): UserLoginResponse {
            $this->validateUserLoginRequest($request);

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("Id atau Password salah");
            }

            if (password_verify($request->password, $user->password)) {
                $response = new UserLoginResponse();
                $response->user = $user;
                return $response;
            }
            else {
                throw new ValidationException("Id atau Password salah");
            }
        }

        private function validateUserLoginRequest(UserLoginRequest $request) {
            if($request->id == null || $request->password == null || trim($request->id == "") || trim($request->password == "")) {
                throw new ValidationException("Id atau Password Tidak Boleh Kosong");
            }
        }

        public function updateProfile(UserUpdateProfileRequest $request): UserUpdateProfileResponse {
            $this->validateUserprofileUpdateRequest($request);

            try {
                Database::beginTransaction();

                $user = $this->userRepository->findById($request->id);
                if($user == null) {
                    throw new ValidationException("User Tidak ditemukan");
                }

                $user->name = $request->name;
                $this->userRepository->update($user);

                Database::commitTransaction();

                $response = new UserUpdateProfileResponse();
                $response->user = $user;

                return $response;
            }
            catch (Exception $exception) {
                Database::rollbackTransaction();
                throw $exception;
            }
        }

        private function validateUserprofileUpdateRequest(UserUpdateProfileRequest $request) {
            if($request->id == null || $request->name == null || trim($request->id == "") || trim($request->name == "")) {
                throw new ValidationException("Id atau Name Tidak Boleh Kosong");
            }
        }

        public function updatePassword(UserUpdatePasswordRequest $request): UserUpdatePasswordResponse {
            $this->validateUserPasswordUpdateRequest($request);

            try {
                Database::beginTransaction();

                $user = $this->userRepository->findById($request->id);
                if($user == null) {
                    throw new ValidationException("User Tidak Ditemukan");
                }

                if(!password_verify($request->oldPassword, $user->password)) {
                    throw new ValidationException("Old Password Salah");
                }

                $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
                $this->userRepository->update($user);

                Database::commitTransaction();

                $response = new UserUpdatePasswordResponse();
                $response->user = $user;
                return $response;
            }
            catch (Exception $exception) {
                Database::rollbackTransaction();
                throw $exception;
            }
        }

        private function validateUserPasswordUpdateRequest(UserUpdatePasswordRequest $request) {
            if($request->id == null || $request->oldPassword == null || $request->newPassword == null || trim($request->id == "") || trim($request->oldPassword == "") || trim($request->newPassword == "")) {
                throw new ValidationException("Id, Old Password, New Password Tidak Boleh Kosong");
            }
        }
    }