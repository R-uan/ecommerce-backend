<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller {

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login() {
        $credentials = request(['email', 'password']);
        $token       = Auth::attempt($credentials);
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        } else {
            return response()->json(['message' => ['token' => $token]], Response::HTTP_OK);
        }
    }
}
