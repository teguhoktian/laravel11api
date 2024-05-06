<?php

namespace App\Http\Controllers;

use App\APIResponseBuilder;
use App\Models\User;
use App\Settings\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request, GeneralSetting $settings): JsonResponse
    {
        $request->merge([
            'field' => $request->input('field', 'id'),
            'direction' => $request->input('direction', 'DESC'),
            'per_page' => $request->input('per_page', $settings->per_page),
        ]);

        $users = User::with(['roles'])->search($request->search)->orderBy($request->field, $request->direction)->paginate($request->per_page);

        return APIResponseBuilder::success([
            'collections' => $users,
            'filters' => request()->all(['search', 'per_page', 'field', 'direction'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return APIResponseBuilder::success($user, __("Data saved successfull."));
    }

    public function update(User $user, Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ",email," . $user->id],
            'role' => ['required']
        ]);

        $request['password'] = $user->password;

        $user->update($request->only('name', 'email', 'password'));

        $user->syncRoles($request->role);

        return APIResponseBuilder::success($user, __("Data updated successfull."));
    }

    public function show(User $user): JsonResponse
    {
        return APIResponseBuilder::success($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $userTmp = $user;
        $user->delete();
        return APIResponseBuilder::success($userTmp, __('Data deleted successfully'));
    }
}
