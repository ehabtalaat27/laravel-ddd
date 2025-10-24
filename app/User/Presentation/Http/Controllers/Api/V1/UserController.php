<?php

namespace App\User\Presentation\Http\Controllers\Api\V1;

use App\Shared\Base\BaseApiController;
use App\User\Domain\Repositories\UserRepositoryInterface;
use App\User\Presentation\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function __construct(UserRepositoryInterface $repository)
    {

        parent::__construct($repository, UserResource::class );
    }
}
