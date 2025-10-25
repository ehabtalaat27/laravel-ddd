<?php

namespace App\User\Application\UseCases\Services;

use App\User\Domain\Repositories\UserRepositoryInterface;

class UserService
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new user with the given request.
     *
     * @param Request $request
     * @return User
     */
    public function create($request)
    {
        $user = $this->userRepository->create($request->validated());

        $user->info()->create($request->validated());

        return $user;
    }
}
