<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
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
            $response = $this->user->withRelationsByAll('roles');
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $user = $request->all();
            $user['password'] = bcrypt($request->password);

            if ($request->hasFile('profile_picture'))
            {
                $user['profile_picture'] =  url('storage') . '/' . $request->file('profile_picture')->store('users');
            }

            $response = $this->user->create($user);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return response()->json($response, Response::HTTP_NO_CONTENT);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
