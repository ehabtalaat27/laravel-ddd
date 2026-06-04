<?php

namespace App\User\Presentation\Http\Controllers\Api\V1;

use App\Shared\Base\BaseApiController;
use App\User\Application\UseCases\Services\UserService;
use App\User\Domain\Repositories\UserRepositoryInterface;
use App\User\Infrastructure\Models\User;
use App\User\Presentation\Http\Requests\Api\V1\UserRequest;
use App\User\Presentation\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    protected $userService;
    public function __construct(UserRepositoryInterface $repository, UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct($repository, UserResource::class);
    }

    /**
     * Create a new user
     *
     * @param \App\User\Presentation\Http\Requests\Api\V1\UserRequest $request
     * @return \App\User\Infrastructure\Models\User
     */
    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request);

        return new UserResource($user);
    }

    /**
     * Retrieve a user by its ID
     *
     * @param int $id
     * @return \App\User\Presentation\Http\Resources\Api\V1\UserResource
     * @throws \Illuminate\Http\Exceptions\NotFoundHttpException
     */
    public function show($id)
    {
        $user = $this->repository->find($id, ['info']);

        if (!$user) {
            return $this->notFound('User not found');
        }
        return new UserResource($user);
    }


    public function deactivate($id)
    {
        $user = $this->userService->deactivate($id);

        return new UserResource($user);
    }
    public function activate($id)
    {
        $user = $this->userService->activate($id);

        return new UserResource($user);
    }
    /**
     * Remove a user by its ID
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Http\Exceptions\NotFoundHttpException
     */
    public function destroy($id)
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return $this->notFound('User not found');
        }
        $this->repository->remove($user);

        return $this->success('User deleted');
    }
}
