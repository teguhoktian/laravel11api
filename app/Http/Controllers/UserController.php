<?php

namespace App\Http\Controllers;

use App\APIResponseBuilder;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $request['field'] = request('field') ?: "id";
        $request['direction'] = request('direction') ?: "ASC";
        $request['per_page'] = request('per_page') ?: env('PER_PAGE');
        $request['search'] = request('search');

        $users = User::with(['roles'])
            ->search($request->search)
            ->orderBy($request->field, $request->direction)
            ->paginate($request->per_page);

        return APIResponseBuilder::success(
            [
                "collections" => $users,
                'filters' => request()->all(['search', 'per_page', 'field', 'direction'])
            ],
            __("Users list successfully loaded.")
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return APIResponseBuilder::success($user, __("Data saved successfull."));
    }

    public function update(User $user, Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ",email," . $user->id]
        ]);

        $request['password'] = $user->password;

        $user->update($request->only('name', 'email', 'password'));

        return APIResponseBuilder::success($user, __("Data updated successfull."));
    }

    public function show(User $user): JsonResponse
    {
        return APIResponseBuilder::success(
            $user->setHidden(
                [
                    'email_verified_at',
                    'password',
                    'remember_token',
                    'updated_at',
                    'roles'
                ]
            )
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $u = $user;
        $user->delete();
        return APIResponseBuilder::success(
            $u->setHidden(
                [
                    'email_verified_at',
                    'password',
                    'remember_token',
                    'updated_at',
                    'roles'
                ]
            ),
            __('Data deleted successfully')
        );
    }
}
