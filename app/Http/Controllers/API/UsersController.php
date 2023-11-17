<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends AbstractApiController
{

    public function __construct(protected UserService $userService) { }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->userService->list();

        return $this->success($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        $user = $this->userService->create($request->validationData());

        return $this->success($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->userService->getById($id);

        return $this->success($user);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $user = $this->userService->update($id, $request->validationData());

        return $this->success($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);

        return $this->success('Successfully deleted!');
    }
}
