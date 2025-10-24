<?php

namespace App\User\Infrastructure\Repositories;

use App\Shared\Base\BaseRepository;
use App\User\Domain\Repositories\UserRepositoryInterface;
use App\User\Infrastructure\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
