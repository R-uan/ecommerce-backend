<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreProductsRequest;
use App\Http\Requests\Update\UpdateProductsRequest;
use App\Models\ProductDetails;
use App\Models\Products;
use App\Services\Filters\ProductsQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller {

  /**
   * Retrieves all products from database
   * @return \Illuminate\Http\JsonResponse
   */
  public function All() {
    try {
      $products = Cache::remember('all_products', now()->addMinutes(60), function () {
        return Products::with('productDetails')
          ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
          ->select('products.*', 'manufacturers.name as manufacturer')
          ->orderBy('name')
          ->paginate(16);
      });

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
  public function One(string $id) {
    try {
      $product = Products::with('productDetails')
        ->where('products.id', $id)
        ->select('products.*')
        ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
        ->select('products.*', 'manufacturers.name as manufacturer')
        ->first();
      if ($product) {
        return response()->json($product, Response::HTTP_OK);
      } else {
        return response()->json('Product ' . $id . ' was not found.', Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went wrong.',
        'error'   => $th->getMessage(),
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Create one product record in the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function Create(StoreProductsRequest $request) {
    try {
      DB::beginTransaction();
      $product_input = [
        'name'              => $request->name,
        'description'       => $request->description,
        'image_url'         => $request->image_url,
        'category'          => $request->category,
        'availability'      => $request->availability,
        'production_time'   => $request->production_time,
        'unit_price'        => $request->unit_price,
        'manufacturers_id'  => $request->manufacturers_id,
        'short_description' => $request->short_description,
        'long_description'  => $request->long_description,
      ];

      $product = new Products($product_input);
      $product->save();

      if ($product) {
        $product_details_input        = $request->product_details;
        $product_details              = new ProductDetails($product_details_input);
        $product_details->products_id = $product->id;
        $product_details->save();
        DB::commit();
        return response()->json([$product, $product_details], Response::HTTP_CREATED);
      } else {
        DB::rollBack();
        return response()->json('Unable to create new product', Response::HTTP_I_AM_A_TEAPOT);
      }

    } catch (\Throwable $th) {
      DB::rollBack();
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
  public function Update(string $id, UpdateProductsRequest $request) {
    try {
      $product = Products::find($id);
      if ($product) {
        $product->update($request->all());
        return response()->json($product, Response::HTTP_OK);
      } else {
        return response()->json('Product %s not found.', Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Delete one product record by id
   * @return \Illuminate\Http\JsonResponse
   */
  public function Destroy(string $id) {
    try {
      $deleted = Products::destroy($id);
      return $deleted == 1 ?
      response()->json(true, Response::HTTP_OK) :
      response()->json(false, Response::HTTP_NOT_MODIFIED);
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
  public function Search(Request $request) {
    try {
      $filter   = new ProductsQuery();
      $query    = $filter->Transform($request);
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

  /**
   * Returns only the necessary amount of data to make a miniature of the product
   * @return \Illuminate\Http\JsonResponse
   */
  public function AllMiniatures(Request $request) {
    try {
      $products = Cache::remember('all_miniatures', now()->addMinutes(60), function () {
        return Products::join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
          ->select([
            'products.id',
            'products.name',
            'products.category',
            'products.image_url',
            'products.unit_price',
            'products.availability',
            'manufacturers.name as manufacturer',
          ])->orderBy('name')->paginate(16);
      });
      return response()->json($products, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Given an Array of IDs Returns only the necessary amount of data to make a miniature of the products
   * @return \Illuminate\Http\JsonResponse
   */
  public function SomeMiniatures(Request $request) {
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
        ])
        ->whereIn('products.id', $request->products)
        ->get();
      if ($products) {
        return response()->json($products, Response::HTTP_OK);
      } else {
        return response()->json('', Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
