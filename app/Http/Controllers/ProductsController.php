<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreProductsRequest;
use App\Http\Requests\Update\UpdateProductsRequest;
use App\Models\Products;
use App\Services\Filters\ProductsQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller {
    #region Public Functions

    /**
     * Retrieves all products from database
     * @return \Illuminate\Http\JsonResponse
     */
    public function all() {
        try {
            $products = Products::with('productDetails')
                ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
                ->select('products.*', 'manufacturers.name as manufacturer')
                ->orderBy('name')
                ->paginate(16);
            return response()->json($products, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Returns only the necessary amount of data to make a miniature of the whole product
     * @return \Illuminate\Http\JsonResponse
     */
    public function miniatures() {
        try {
            $products = Products::join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
                ->select([
                    'products.id',
                    'products.name',
                    'products.category',
                    'products.image_url',
                    'products.availability',
                    'products.unit_price',
                    'manufacturers.name as manufacturer',
                ])->orderBy('name')->paginate(16);
            return response()->json($products, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieves one product by the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function one(string $id) {
        try {
            $product = Products::with('productDetails')
                ->where('products.id', $id)
                ->select('products.*')
                ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
                ->select('products.*', 'manufacturers.name as manufacturer')
                ->get();
            if ($product) {
                return response()->json($product, Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('Product %s not found', $id),
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
            $filter   = new ProductsQuery();
            $query    = $filter->transform($request);
            $products = Products::where($query)
                ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
                ->select('products.*', 'manufacturers.name as manufacturer')
                ->orderBy('name')
                ->paginate(16)->withQueryString();
            return response()->json($products, Response::HTTP_OK);
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
     * Create one product record in the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductsRequest $request) {
        try {
            $product = new Products($request->all());
            $saved   = $product->save();
            if ($saved) {
                return response()->json([
                    'message' => sprintf('Sucessfuly saved new Product %s.', $request->name),
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'message' => 'Failed to save new Product.',
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
     * Update one product record given the id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $id, UpdateProductsRequest $request) {
        try {
            $product = Products::find($id);
            if ($product) {
                $product->update($request->all());
                return response()->json(['message' => [
                    'message' => sprintf('Product %s has been updated', $id),
                    'data'    => $product,
                ]], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('Product %s not found.', $id),
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
     * Delete one product record by id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id) {
        try {
            $deleted = Products::destroy($id);
            if ($deleted == 0) {
                return response()->json([
                    'message' => sprintf('Product %s not found.', $id),
                ], Response::HTTP_NOT_FOUND);
            } else {
                return response()->json([
                    'message' => sprintf('Product %s was sucessfuly deleted.', $id),
                ]);
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
