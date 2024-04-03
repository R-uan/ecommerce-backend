<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressController extends Controller {
  public function Create(Request $request) {
    try {
      $auth_header = explode(" ", $request->header('Authorization'));
      $user        = JWTAuth::parseToken($auth_header[1])->authenticate();
      dd($user);
      if ($user) {
        DB::beginTransaction();
        $address_info = [
          'planet'       => $request->planet,
          'nation'       => $request->nation,
          'state'        => $request->state,
          'city'         => $request->city,
          'sector'       => $request->sector,
          'residence_id' => $request->residence_id,
        ];
        $address = new Address($address_info);
        $address->save();
        $cry = User::find($user->id);
        $cry->update(['address_id' => $address->id]);
        $cry->address_id == $address->id ? DB::commit() : DB::rollBack();
        return $cry->address_id == $address->id ?
        response()->json(true, HttpResponse::HTTP_OK) :
        response()->json(false, HttpResponse::HTTP_NOT_MODIFIED);
      }

      return response()->json(false, HttpResponse::HTTP_UNAUTHORIZED);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
