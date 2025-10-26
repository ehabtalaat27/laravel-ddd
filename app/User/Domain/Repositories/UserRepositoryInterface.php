<?php

namespace App\User\Domain\Repositories;

use App\Shared\Base\BaseRepositoryInterface;
use App\User\Domain\Entities\UserEntity;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findEntity(int $id): ?UserEntity;
    public function save(UserEntity $entity);
    public function saveWithInfo(UserEntity $entity,  $infoData = []);
}
