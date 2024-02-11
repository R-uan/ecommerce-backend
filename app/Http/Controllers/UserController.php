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
    #region Public Functions

    /**
     * Create a user record in the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request) {
        try {
            $input         = $request->all();
            $input['role'] = 'CLIENT';
            $user          = new User($input);
            $saved         = $user->save();
            if ($saved) {
                $token = JWTAuth::fromUser($user);
                return response()->json([
                    'message'   => 'User successfully created.',
                    'user'      => $user,
                    'authToken' => $token,
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'message' => "Failed to register user.",
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gets information of the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile() {
        try {
            $authenticatedUser = auth()->user();
            if ($authenticatedUser) {
                return response()->json([
                    'message' => 'Profile found.',
                    'user'    => $authenticatedUser,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Could not authenticate user',
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update user given successful authentication
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request) {
        try {
            $authenticatedUser = auth()->user();
            if ($authenticatedUser) {
                DB::beginTransaction();
                $new_user_information = $request->all();
                $user                 = User::find($authenticatedUser->id);
                if ($user->update($new_user_information)) {
                    DB::commit();
                    return response()->json([
                        'message' => 'User was updated successfully.',
                        'user'    => $user,
                    ], Response::HTTP_OK);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Something went wrong during the update.',
                    ], Response::HTTP_NOT_MODIFIED);
                }
            } else {
                return response()->json([
                    'message' => 'Could not authenticate user.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'something went wrong',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion

    #region Administrative Functions

    /**
     * Gets all user records
     * @return \Illuminate\Http\JsonResponse
     */
    public function all() {
        try {
            $users = User::all()->paginate();
            return response()->json([
                'message' => 'Users found.',
                'users'   => $users,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gets one user record given the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function one(string $id) {
        try {
            $user = User::find($id);
            if ($user) {
                return response()->json([
                    'message' => sprintf('User %s found.', $id),
                    'user'    => $user,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('User %s was not found.', $id),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Deletes one user record given the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id) {
        try {
            $deleted = User::destroy($id);
            if ($deleted) {
                return response()->json([
                    'message' => sprintf('User %s was successfully deleted.', $id),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('User %s was not found', $id),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'something went wrong',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion
}