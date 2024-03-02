<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreManufacturersRequest;
use App\Http\Requests\Update\UpdateManufacturersRequest;
use App\Models\Manufacturers;
use App\Services\Filters\ManufacturersQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ManufacturersController extends Controller {
    #region Public Functions

    /**
     * Gets all manufacturer records in the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function all() {
        try {
            $manufacturers = Manufacturers::select('manufacturers.*')
                ->orderBy('id')
                ->paginate();
            return response()->json([
                'message' => 'Manufacturers found.',
                'data'    => $manufacturers,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gets one manufacturer record from the database given the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function one(string $id) {
        try {
            $manufacturer = Manufacturers::find($id);
            if ($manufacturer) {
                return response()->json([
                    'message' => 'Manufacturer found',
                    'data'    => $manufacturer,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('Manufacturer %s not found.', $id),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Performs a search for orders based on specific criteria provided in the request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        try {
            $filter        = new ManufacturersQuery();
            $query         = $filter->transform($request);
            $manufacturers = Manufacturers::where($query)->paginate();
            if ($manufacturers) {
                return response()->json([
                    'message' => 'Manufacturers Found.',
                    'data'    => $manufacturers,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Nothing found',
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Gets first 10 products based on the manufacturer
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(string $id) {
        try {
            $response = Manufacturers::where('manufacturers.id', $id)
                ->join('products', 'products.manufacturers_id', '=', 'manufacturers.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.category',
                    'products.image_url',
                    'products.unit_price',
                    'products.availability',
                    'manufacturers.name as manufacturer'
                )->take(10)->get();
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion

    #region Administrative Functions

    /**
     * Create one manufacturer record on the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreManufacturersRequest $request) {
        try {
            $manufacturer = new Manufacturers($request->all());
            $saved        = $manufacturer->save();
            if ($saved) {
                return response()->json([
                    'message' => sprintf('Sucessfuly saved new Manufacturer %s.', $request->name),
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'message' => 'Failed to save manufacturer.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update one manufacturer record on the database given the id and request information
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateManufacturersRequest $request, string $id) {
        try {
            $manufacturer = Manufacturers::find($id);
            if ($manufacturer) {
                $manufacturer->update($request->all());
                return response()->json([
                    'message' => sprintf('Manufacturer %s has been updated.', $id),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('Manufacturer %s no found.', $id),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Deletes one manufacturer record given the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id) {
        try {
            $deleted = Manufacturers::destroy($id);
            if ($deleted == 0) {
                return response()->json([
                    'message' => sprintf('Manufacturer %s was not found.', $id),
                ], Response::HTTP_NOT_FOUND);
            } else {
                return response()->json([
                    'message' => sprintf('Manufacturer %s was sucessfuly deleted.', $id),
                ], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion
}
