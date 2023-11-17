<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    public function __construct(protected UserService $userService) { }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->userService->list();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $edit = false;

        return view('users.form', compact('edit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        $user = $this->userService->create($request->validationData());

        return redirect()->route('users.edit', $user->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit = true;

        $user = $this->userService->getById($id);

        return view('users.form', compact('edit', 'user'));
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $user = $this->userService->update($id, $request->validationData());

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);

        return redirect()->route('users.index');
    }
}
