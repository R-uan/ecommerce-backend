<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller {
  public function Create(Request $request) {
    try {
      $user = $user = auth()->user();
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
        response()->json(true, Response::HTTP_CREATED) :
        response()->json(false, Response::HTTP_NOT_MODIFIED);
      }
      return response()->json(false, Response::HTTP_UNAUTHORIZED);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function One() {
    try {
      $user    = auth()->user();
      $address = Address::find($user->address_id);
      return $address ?
      response()->json($address, Response::HTTP_OK) :
      response()->json(false, Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function Update(Request $request) {
    try {
      $user         = auth()->user();
      $address_info = $request->all();
      $address      = Address::find($user->address_id);
      if ($address) {
        DB::beginTransaction();
        $address->update($address_info);
        DB::commit();
        return response()->json(true, Response::HTTP_OK);
      } else {
        return response()->json(false, Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function Destroy() {
    try {
      $user    = auth()->user();
      $deleted = Address::destroy($user->address_id);
      return $deleted == 1 ?
      response()->json(true, Response::HTTP_OK) :
      response()->json(false, Response::HTTP_NOT_MODIFIED);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
