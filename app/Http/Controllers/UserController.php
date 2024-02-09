<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller {
    /**
     * Client User Registration
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request) {
        $input         = $request->all();
        $input['role'] = 'CLIENT';
        $user          = new User($input);
        $saved         = $user->save();
        if ($saved) {
            $token = JWTAuth::fromUser($user);
            return response()->json(['message' => ['user' => $user, 'authToken' => $token]], Response::HTTP_CREATED);
        } else {
            return response()->json(['message' => "Failed to register user"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Gets All Users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        return response()->json(User::all());
    }
}
