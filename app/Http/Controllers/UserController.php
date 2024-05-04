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
        $users = User::with(['roles'])
            ->search($request->search)
            ->orderBy('id', 'DESC')
            ->paginate($request->per_page ?: env('PER_PAGE'));

        return APIResponseBuilder::success(
            $users->setHidden(
                [
                    'email_verified_at',
                    'password',
                    'remember_token',
                    'updated_at',
                    'roles'
                ]
            ),
            __("Users list successfully loaded.")
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) return APIResponseBuilder::invalidData(__('Unprocessable Entity'), $validator->messages());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return APIResponseBuilder::success($user, __("Data saved successfull."));
    }

    public function update(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ",email," . $user->id]
        ]);

        if ($validator->fails()) return APIResponseBuilder::invalidData(__('Unprocessable Entity'), $validator->messages());

        $request['password'] = $user->password;
        $user->update($request->only('name', 'email', 'password'));

        return APIResponseBuilder::success($user, __("Data updated successfull."));
    }

    public function show(User $user)
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

    public function destroy(User $user)
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
