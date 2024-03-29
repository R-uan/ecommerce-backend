<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanetDestinationRequest;
use App\Models\PlanetDestination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

class PlanetDestinationController extends Controller {

  /**
   * Create one destination
   * @return \Illuminate\Http\JsonResponse
   */
  public function Create(StorePlanetDestinationRequest $request) {
    try {
      $destination = new PlanetDestination($request->all());
      $saved       = $destination->save();
      if ($saved) {
        return response()
          ->json($saved, Response::HTTP_OK);
      } else {
        return response()
          ->json('Unable to save the destination.', Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()
        ->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Find one record
   * @return Illuminate\Http\JsonResponse
   */
  public function One(string $name) {
    try {

      $planet = PlanetDestination::where('name', 'ILIKE', '%' . $name . '%')->get();
      if ($planet) {
        return response()
          ->json($planet, Response::HTTP_OK);
      } else {
        return response()
          ->json('Destination not found.', Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()
        ->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Find all records
   * @return Illuminate\Http\JsonResponse
   */
  public function All() {
    try {
      $destinations = PlanetDestination::all();
      return response()->json($destinations, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Delete a record
   * @return Illuminate\Http\JsonResponse
   */
  public function Delete(string $id) {
    try {
      $planet = PlanetDestination::find($id)->name;
      $delete = PlanetDestination::destroy($id);
      return $delete == 1 ?
      response()->json(sprintf('Destination %s deleted.', $planet), Response::HTTP_OK) :
      response()->json('Destination not found.', Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Update a record
   * @return Illuminate\Http\JsonResponse
   */
  public function Update(Request $request, $id) {
    try {
      DB::beginTransaction();
      $destination = PlanetDestination::find($id);
      $destination->Update($request->all());
      if ($destination) {
        DB::commit();
        return response()->json($destination, Response::HTTP_OK);
      } else {
        DB::rollBack();
        return response()->json('shrug', Response::HTTP_NO_CONTENT);
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()
        ->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
