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
     * Get All api/products
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        return Products::join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer_name')
            ->orderBy('id')
            ->paginate();
    }

    /**
     * Get One api/products/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id) {
        $product = Products::find($id);
        if ($product) {
            return response()->json($product, Response::HTTP_OK);
        } else {
            return response()->json(['message' => sprintf('Product %s not found', $id)], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Search by parameter api/products/search?param[operation]=value
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $filter   = new ProductsQuery();
        $query    = $filter->transform($request);
        $products = Products::where($query)
            ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer')
            ->paginate();
        return response()->json($products, Response::HTTP_OK);
    }

    #endregion

    #region Administrative Functions

    /**
     * Post One api/products
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductsRequest $request) {
        $product = new Products($request->all());
        $saved   = $product->save();
        if ($saved) {
            return response()->json(['message' => sprintf('Sucessfuly saved new Product %s.', $request->name)], Response::HTTP_CREATED);
        } else {
            return response()->json(['message' => 'Failed to save new Product.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update One api/products/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $id, UpdateProductsRequest $request) {
        $product = Products::find($id);
        if ($product) {
            $product->update($request->all());
            return response()->json(['message' => sprintf('Product %s has been updated', $id)], Response::HTTP_OK);
        } else {
            return response()->json(['message' => sprintf('Product %s not found.', $id)], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete One api/products/{id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id) {
        $deleted = Products::destroy($id);
        if ($deleted == 0) {
            return response()->json(['message' => sprintf('Product %s not found.', $id)], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json(['message' => sprintf('Product %s was sucessfuly deleted.', $id)]);
        }
    }

    #endregion
}
