<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Login user.
     *
     * @param  Login  $request
     * @return UserResource
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email');

        $customer = User::where('email', $credentials['email'])->first();

        if (!$customer) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        // Create access token
        $token = $customer->createToken('minibank-access-token')
            ->plainTextToken;

        return (new UserResource($customer))
            ->additional(['meta' => ['access_token' => $token]]);
    }

    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Revoke access tokens
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
