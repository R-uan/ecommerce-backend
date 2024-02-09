<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManufacturersRequest;
use App\Http\Requests\UpdateManufacturersRequest;
use App\Models\Manufacturers;
use App\Services\Filters\ManufacturersQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ManufacturersController extends Controller {
    /**
     * Get All api/manufacturers
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        return Manufacturers::select('manufacturers.*')
            ->orderBy('id')
            ->paginate();
    }

    /**
     * Post One api/manufacturers
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreManufacturersRequest $request) {
        $manufacturer = new Manufacturers($request->all());
        $saved        = $manufacturer->save();
        if ($saved) {
            return response()->json(['message' => sprintf('Sucessfuly saved new Manufacturer %s.', $request->name)], Response::HTTP_CREATED);
        } else {
            return response()->json(['message' => 'Failed to save manufacturer.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get One api/manufacturers/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id) {
        $manufacturer = Manufacturers::find($id);
        if ($manufacturer) {
            return response()->json($manufacturer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => sprintf('Manufacturer %s not found.', $id)], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update One api/manufacturers/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateManufacturersRequest $request, string $id) {
        $manufacturer = Manufacturers::find($id);
        if ($manufacturer) {
            $manufacturer->update($request->all());
            return response()->json(['message' => sprintf('Manufacturer %s has been updated.', $id)], Response::HTTP_OK);
        } else {
            return response()->json(['message' => sprintf('Manufacturer %s no found.', $id)], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete One api/manufacturers/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id) {
        $deleted = Manufacturers::destroy($id);
        if ($deleted == 0) {
            return response()->json(['message' => sprintf('Manufacturer %s was not found.', $id)], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json(['message' => sprintf('Manufacturer %s was sucessfuly deleted.', $id)], Response::HTTP_OK);
        }
    }

    /**
     * Search Query api/manufacturers/search?param[operation]=value
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $filter        = new ManufacturersQuery();
        $query         = $filter->transform($request);
        $manufacturers = Manufacturers::where($query)->paginate();
        if ($manufacturers) {
            return response()->json($manufacturers, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Nothing found'], Response::HTTP_NOT_FOUND);
        }
    }
}
