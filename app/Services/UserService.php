<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\UserRepositoryInterface;

class UserService
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    public function create(UserDTO $userDTO)
    {
        return $this->userRepository->create($userDTO);
    }
}
