<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/* Feature Tests Done */
class AuthenticationController extends Controller {
  /**
   * Gets a JWT via given credentials.
   * @return \Illuminate\Http\JsonResponse
   */
  public function Login(Request $request) {
    try {
      $credentials = ['email' => $request->email, 'password' => $request->password];
      $token       = auth()->attempt($credentials);
      return $token ?
      response()->json($token, Response::HTTP_OK) :
      response()->json('Unauthorized.', Response::HTTP_UNAUTHORIZED);
    } catch (Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json('Invalid token received.', Response::HTTP_UNAUTHORIZED);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json('Token expired.', Response::HTTP_NOT_EXTENDED);
      } else {
        return response()->json('Token was not found.', Response::HTTP_UNAUTHORIZED);
      }
    }
  }

  /**
   * Refresh Token if >1 week old
   * @return \Illuminate\Http\JsonResponse
   */
  public function Refresh() {
    try {
      $token     = JWTAuth::getToken();
      $new_token = JWTAuth::refresh($token);
      return $new_token ?
      response()->json($new_token, Response::HTTP_OK) :
      response()->json('Unable to refresh token.', Response::HTTP_UNAUTHORIZED);
    } catch (Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json('Invalid Token.', Response::HTTP_UNAUTHORIZED);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json('Cannot Refresh.', Response::HTTP_UNAUTHORIZED);
      } else {
        return response()->json('Token was not found.', Response::HTTP_UNAUTHORIZED);
      }
    }
  }
}
