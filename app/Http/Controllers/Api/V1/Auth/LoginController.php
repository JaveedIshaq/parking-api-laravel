<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
 */
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {

        // we will fill in a bot later

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect'],
            ]);
        }

        $device = substr($request->userAgent() ?? '', 0, 255);
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));

        return response()->json([
            "status" => true,
            "message" => "user logged in successfully",
            "access_token" => $user->createToken($device, expiresAt: $expiresAt)->plainTextToken,
        ], Response::HTTP_OK);
    }

}
