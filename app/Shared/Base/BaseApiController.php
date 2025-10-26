<?php

namespace App\Shared\Base;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller
{
    use ApiTrait;

    protected BaseRepositoryInterface $repository;
    protected bool $order = true;
    protected array $relations = [];

    protected string $modelResource;


    /**
     * Constructor
     *
     * @param BaseRepositoryInterface $repository
     * @param string $modelResource
     *
     * @return void
     */
    public function __construct(BaseRepositoryInterface $repository, string $modelResource)
    {
        $this->repository = $repository;
        $this->modelResource = $modelResource;

        if (request()->has('relations')) {
            $this->parseIncludes(request('relations'));
        }
    }

    /**
     * Show the list of models.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();

        $data = [
            'page'   => $request->get('page', 1),
            'limit'  => $request->get('limit', 10),
            'order'  => $request->get('order', []),
        ];

        $models = $this->repository->search($filters, $this->relations, $data);




        return $this->respondWithCollection($models);
    }

    /**
     * Parse the relations string into an array and assign it to the $this->relations property
     *
     * @param string $relations Comma-separated string of relations to include
     * @return void
     */
    protected function parseIncludes(string $relations): void
    {
        $this->relations = array_map('trim', explode(',', $relations));
    }


    protected function respondWithCollection($collection, int $statusCode = null, array $headers = []): mixed
    {
        $statusCode = $statusCode ?? Response::HTTP_OK;
        $resources = forward_static_call([$this->modelResource, 'collection'], $collection);
        return $resources;
    }
}
