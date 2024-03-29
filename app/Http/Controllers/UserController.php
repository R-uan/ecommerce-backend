<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreUserRequest;
use App\Http\Requests\Update\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller {
  /**
   * Create a user record in the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function Register(StoreUserRequest $request) {
    try {
      $input         = $request->all();
      $input['role'] = 'CLIENT';
      $user          = new User($input);
      $saved         = $user->save();
      if ($saved) {
        $token = JWTAuth::fromUser($user);
        return response()->json($token, Response::HTTP_CREATED);
      } else {
        return response()->json("Failed to register user.", Response::HTTP_BAD_REQUEST);
      }
    } catch (\Throwable $th) {
      return $th->getCode() == 23505 ?
      response()->json("Email already registered.", Response::HTTP_CONFLICT) :
      response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Gets information of the authenticated user
   * @return \Illuminate\Http\JsonResponse
   */
  public function Profile() {
    try {
      $authenticated_user = auth()->user();
      return $authenticated_user ?
      response()->json($authenticated_user, Response::HTTP_OK) :
      response()->json('Could not authenticate user.', Response::HTTP_UNAUTHORIZED);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Update user given successful authentication
   * @return \Illuminate\Http\JsonResponse
   */
  public function Update(UpdateUserRequest $request) {
    try {
      $authenticated_user = auth()->user();
      if ($authenticated_user) {
        DB::beginTransaction();
        $new_user_information = $request->all();
        $user                 = User::find($authenticated_user->id);
        if ($user->update($new_user_information)) {
          DB::commit();
          return response()->json($user, Response::HTTP_OK);
        } else {
          DB::rollBack();
          return response()->json('Unable to update the user.', Response::HTTP_NOT_MODIFIED);
        }
      } else {
        return response()->json('Could not authenticate user.', Response::HTTP_UNAUTHORIZED);
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Gets all user records
   * @return \Illuminate\Http\JsonResponse
   */
  public function All() {
    try {
      $users = User::all()->paginate();
      return response()->json($users, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Gets one user record given the id
   * @return \Illuminate\Http\JsonResponse
   */
  public function One(string $id) {
    try {
      $user = User::find($id);
      return $user ?
      response()->json($user, Response::HTTP_OK) :
      response()->json(sprintf('User %s was not found.', $id), Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Deletes one user record given the id
   * @return \Illuminate\Http\JsonResponse
   */
  public function Destroy(string $id) {
    try {
      $deleted = User::destroy($id);
      $deleted == 1 ?
      response()->json(sprintf('User %s was successfully deleted.', $id), Response::HTTP_OK) :
      response()->json(sprintf('User %s was not found.', $id), Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
