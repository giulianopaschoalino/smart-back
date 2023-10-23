<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ImportUsersWithSmartUsersRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Imports\UsersWithSmartUsersImport;
use App\Repositories\Users\UserContractInterface;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserContractInterface $user
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $response = $this->user->getOrdered();
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
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
            $data = $request->all();
            $data['password'] = bcrypt($request->password);

            if (!$request->hasFile('profile_picture')) {
                return $this->errorResponse(false, '', 500);
            }
            $file = $request->file('profile_picture');
            $path = $file->storeAs('avatars', $file->hashName(), 's3');

            $data['profile_picture'] =  Storage::disk('s3')->url($path);
            $response = $this->user->create($data);
            $response->roles()->sync($data['role']);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $ex) {
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
        } catch (\Exception $ex) {
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
            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $response = $this->user->update($data, $id);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        } catch (\Exception $ex) {
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
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function importUserControll(ImportUsersWithSmartUsersRequest $request): JsonResponse
    {
        /**
         * @var \Illuminate\Http\UploadedFile $file
         */
        $file_users = $request->file('file_users');
        /**
         * @var \Illuminate\Http\UploadedFile $file
         */
        $file_logos = $request->file('file_logos');

        $driver = 'imports';
        $user_imports_path = $file_users->store($driver);

        $filename = preg_replace("/imports\//", "", $user_imports_path);

        Excel::import(new UsersWithSmartUsersImport($file_logos), $user_imports_path, $driver);

        // Storage::disk($driver)->delete($filename);

        return response()
            ->json(['message' => 'Dados importados com sucesso!'])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
