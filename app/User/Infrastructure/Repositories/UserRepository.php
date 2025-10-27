<?php

namespace App\User\Infrastructure\Repositories;

use App\Shared\Base\BaseRepository;
use App\User\Domain\Entities\UserEntity;
use App\User\Domain\Repositories\UserRepositoryInterface;
use App\User\Infrastructure\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
    public function findEntity(int $id): ?UserEntity
    {
        $model = User::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function save(UserEntity $entity)
    {
        $model = $entity->id ? User::find($entity->id) : new User();

        $model->fill($entity->toArray());
        $model->save();

        $entity->id = $model->id;
        $model->refresh();

        return $model;
    }
    // EloquentUserRepository
    public function saveWithInfo(UserEntity $entity,  $infoData = [])
    {
        $userModel = $entity->id ? User::find($entity->id) : new User();
        $userModel->fill($entity->toArray());
        $userModel->save();

        if (!empty($infoData)) {
            $userModel->info()->updateOrCreate([], $infoData);
        }
        $userModel->refresh();

        return $userModel->load('info');
    }


    protected function toEntity(User $model): UserEntity
    {
        return new UserEntity($model->toArray());
    }
}
