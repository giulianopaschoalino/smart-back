<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Users\UserContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserContractInterface $user
    ){}

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $list = $this->user->withRelationsByAll('roles');
            return response()->json($list, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $response = $this->user->create($request->all());

            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $response = $this->user->find($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->user->update($request->all(), $id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->user->destroy($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }
}
