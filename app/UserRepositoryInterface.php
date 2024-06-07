<?php

namespace App;

use App\DTO\UserDTO;

interface UserRepositoryInterface
{
    public function create(UserDTO $userDTO);
}
