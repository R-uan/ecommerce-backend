<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationController extends Controller {
    #region Public Function

    /**
     * Gets a JWT via given credentials.
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $credentials = ['email' => $request->email, 'password' => $request->password];
        $token       = auth()->attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            return response()->json([
                'message' => 'Authentication Sucessful',
                'token'   => $token,
            ], Response::HTTP_OK);
        }
    }

    #endregion

    #region System Functions

    /**
     * Refresh Token if >1 week old
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request) {
        try {
            $token    = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            if ($newToken) {
                return response()->json([
                    'message' => 'Token refreshed.',
                    'token'   => $newToken,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'messsage' => 'Unable to refresh token.',
                ], Response::HTTP_NOT_EXTENDED);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'messsage' => 'Invalid Token.',
                ], Response::HTTP_UNAUTHORIZED);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'messsage' => 'Cannot Refresh.',
                ], Response::HTTP_NOT_EXTENDED);
            } else {
                return response()->json([
                    'messsage' => 'Authorization Token not found.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }

    #endregion
}
