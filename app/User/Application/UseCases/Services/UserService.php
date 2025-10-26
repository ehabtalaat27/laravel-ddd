<?php

namespace App\User\Application\UseCases\Services;

use App\User\Domain\Entities\UserEntity;
use App\User\Domain\Entities\UserInfoEntity;
use App\User\Domain\Repositories\UserRepositoryInterface;

class UserService
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

/**
 * Create a new user
 *
 * @param \Illuminate\Http\Request $request
 * @return \App\User\Domain\Entities\UserEntity
 */
    public function create($request)
    {
        $data = $request->validated();

        $user = UserEntity::fromArray($data);

        $userInfo = UserInfoEntity::fromArray($data);

        return $this->userRepository->saveWithInfo($user, $userInfo->toArray() ?? []);
    }

/**
 * Deactivate a user by its ID
 *
 * @param int $id
 *
 * @return \App\User\Domain\Entities\UserEntity
 *
 * @throws \Exception
 */
    public function deactivate(int $id)
    {
        // Get user entity from repo
        $user = $this->userRepository->findEntity($id);

        if (! $user) {
            throw new \Exception('User not found.');
        }

        $user->deactivate();

        return $this->userRepository->save($user);
    }
}
