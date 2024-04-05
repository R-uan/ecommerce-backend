<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
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
        response()->json(true, HttpResponse::HTTP_OK) :
        response()->json(false, HttpResponse::HTTP_NOT_MODIFIED);
      }
      return response()->json(false, HttpResponse::HTTP_UNAUTHORIZED);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function One() {
    try {
      $user    = auth()->user();
      $address = Address::find($user->address_id);
      return $address ?
      response()->json($address, HttpResponse::HTTP_OK) :
      response()->json(false, HttpResponse::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
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
        return response()->json(true, HttpResponse::HTTP_OK);
      } else {
        return response()->json(false, HttpResponse::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
