<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Models\Products;
use App\Services\ProductsQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller {
    # Get All api/products
    public function index(Request $request) {
        return Products::join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer_name')
            ->orderBy('id')
            ->get();
    }
    # Post One api/products
    public function store(StoreProductsRequest $request) {
        $product = new Products($request->all());
        $saved   = $product->save();
        if ($saved) {
            return response()->json(['message' => 'Sucessfuly saved new Product'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Failed to save new Product']);
        }
    }
    # Get One api/products/{id}
    public function show(string $id) {
        $product = Products::find($id);
        if (!isset($product)) {
            return response()->json(['message' => sprintf('Product %s not found', $id)], Response::HTTP_NOT_FOUND);
        }
        return response()->json($product, Response::HTTP_OK);
    }
    # Update One api/products/{id}
    public function update(string $id, UpdateProductsRequest $request) {
        $product = Products::find($id);
        if ($product) {
            $product->update($request->all());
            return response()->json(['message' => sprintf('Product %s has been updated', $id)], Response::HTTP_OK);
        } else {
            return response()->json(['message' => sprintf('Product %s has been updated', $id)], Response::HTTP_OK);
        }
    }
    # Delete One api/products/{id}
    public function destroy(string $id) {
        $deleted = Products::destroy($id);
        if ($deleted == 0) {
            return response()->json(['message' => sprintf('Product %s not found', $id)], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => sprintf('Product %s deleted', $id)]);
    }
    # Search by parameter api/products/search?param[operation]=value
    public function search(Request $request) {
        $filter   = new ProductsQuery();
        $query    = $filter->transform($request);
        $products = Products::where($query)
            ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer')
            ->paginate();
        return response()->json($products, Response::HTTP_OK);
    }
}
