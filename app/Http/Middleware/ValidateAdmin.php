<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateAdmin {
  /**
   * Validates ADMIN from a JWT
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    try {
      $auth_header = explode(" ", $request->header('Authorization'));
      $valid_user  = JWTAuth::parseToken($auth_header[1])->authenticate();
      if ($valid_user) {
        return $valid_user->role == "ADMIN" ?
        $next($request) :
        response()->json('Prohibited access.', Response::HTTP_UNAUTHORIZED);
      } else {
        return response()->json('Unable to authenticate user.', Response::HTTP_UNAUTHORIZED);
      }
    } catch (Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json('Invalid Token.', Response::HTTP_UNAUTHORIZED);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json('Expired Token.', Response::HTTP_UNAUTHORIZED);
      } else {
        return response()->json('Authorization Token not found.', Response::HTTP_UNAUTHORIZED);
      }
    }
  }
}
