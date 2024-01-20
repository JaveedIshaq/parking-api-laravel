<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @group Auth
 */
class RegisterController extends Controller
{
    //

    public function __invoke(Request $request)
    {

        // we will fill in a bot later

        $request->validate([
            'name' => ['required', 'string', 'max:225'],
            'email' => ['required', 'string', 'email', 'max:225', 'unique:users'],
            'password' => ['required', 'confirmed', Password::Defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json([
            "status" => true,
            "message" => "user registered successfully",
            "access_token" => $user->createToken($device)->plainTextToken,
        ], Response::HTTP_CREATED);
    }
}
