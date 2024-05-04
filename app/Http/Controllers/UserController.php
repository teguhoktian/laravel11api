<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function index(Request $request)
    {
        return User::with(['roles'])
            ->search($request->search)
            ->orderBy('id', 'DESC')
            ->paginate($request->per_page ?: env('PER_PAGE'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Data berhasil disimpan.')
        ], 200);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ",email," . $user->id]
        ]);

        $request['password'] = $user->password;
        $user->update($request->only('name', 'email', 'password'));

        return response()->json([
            'success' => true,
            'message' => __('Data berhasil diubah.')
        ], 200);
    }

    public function show(User $user)
    {
        $user->role = $user->roles->pluck(['name']);
        return $user->setHidden(['email_verified_at', 'password', 'remember_token', 'updated_at', 'roles']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('Data berhasil dihapus.')
        ], 200);
    }
}
