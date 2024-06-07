<?php

namespace App\Repositories;

use App\DTO\UserDTO;
use App\Models\User;
use App\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function create(UserDTO $userDTO)
    {
        return $this->model->create($userDTO->toArray());
    }
}
