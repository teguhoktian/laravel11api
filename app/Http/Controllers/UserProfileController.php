<?php

namespace App\Http\Controllers;

use App\APIResponseBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        //

        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'confirmed'],
        ]);

        $password = ($request->password) ? Hash::make($request->password) :  $user->password;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
        ]);

        return APIResponseBuilder::success($user);
    }
}
