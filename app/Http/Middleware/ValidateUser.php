<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateUser {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        try {
            $authHeader = explode(" ", $request->header('Authorization'));
            $validUser  = JWTAuth::parseToken($authHeader[1])->authenticate();
            if ($validUser) {
                return $validUser->role == "USER" ? $next($request) : response()->json(['message' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
            } else {
                return response()->json(['messsage' => 'Invalid Token'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['messsage' => 'Invalid Token.'], Response::HTTP_UNAUTHORIZED);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['messsage' => 'Expired Token.'], Response::HTTP_UNAUTHORIZED);
            } else {
                return response()->json(['messsage' => 'Authorization Token not found.'], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
