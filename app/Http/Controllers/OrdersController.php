<?php

namespace App\Http\Controllers;

use App\Http\Requests\Update\UpdateOrdersRequest;
use App\Models\OrderItens;
use App\Models\Orders;
use App\Models\Products;
use App\Services\Filters\OrdersQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller {
  #region Authenticated Client Function

  /**
   * Creates a new order record under the authenticated client
   * @return \Illuminate\Http\JsonResponse
   */
  public function Create(Request $request) {
    $user_id = auth()->user()->id;
    DB::beginTransaction();
    try {
      $initial_order_info = [
        'total'              => 0,
        'order_date'         => now(),
        'client_id'          => $user_id,
        'status'             => 'Pending',
        'payment_method'     => $request->payment_method,
        'planet_destination' => $request->planet_destination,
      ];

      $order = new Orders($initial_order_info);
      $order->save();
      $total_price = 0;

      foreach ($request->products as $cart_product) {
        $product = Products::find($cart_product['product_id']);
        if ($product && $product->availability == true) {
          $item = [
            'orders_id'   => $order->id,
            'product_id'  => $product->id,
            'unit_price'  => $product->unit_price,
            'amount'      => $cart_product['amount'],
            'total_price' => $product->unit_price * $cart_product['amount'],
          ];

          $total_price += $item['total_price'];
          $order_itens = new OrderItens($item);
          $order_itens->save();
        } else {
          DB::rollBack();
          return response()->json([
            'message'             => 'One of the products is not available',
            'unavailable_product' => [
              'product_id'   => $cart_product['product_id'],
              'product_info' => $product ? $product : null,
            ],
          ], Response::HTTP_BAD_REQUEST);
        }
      }

      $order->total = $total_price;
      $order->save();
      DB::commit();

      return response()->json($order, Response::HTTP_OK);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json([
        'message' => 'Something went wrong.',
        'error'   => $th->getMessage(),
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get all order records from the authenticated client
   * @return \Illuminate\Http\JsonResponse
   */
  public function ClientOrders() {
    try {
      $user  = auth()->user();
      $id    = $user->id;
      $query = Orders::where('client_id', $id)->with('orderItens')->get();
      return response()->json($query, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went wrong.',
        'error'   => $th->getMessage(),
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Retrieves one order record from the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function One(Request $request, string $id) {
    try {
      $user_id = auth()->user()->id;
      $order   = Orders::where('id', $id)
        ->where('client_id', $user_id)
        ->with([
          'OrderItens.products'              => function ($query) {
            $query->select('id', 'name', 'category', 'image_url', 'manufacturers_id');
          },
          'OrderItens.products.manufacturer' => function ($query) {
            $query->select('id', 'name');
          },
        ])
        ->first();
      if (!$order) {
        return response()->json([
          'message' => sprintf('Order %s not found!', $id),
        ], Response::HTTP_NOT_FOUND);
      }

      return response()->json($order, Response::HTTP_OK);
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
   * Retrieves all orders records from the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function All() {
    try {
      $orders = Orders::all()->paginate();
      return response()->json($orders, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went wrong.',
        'error'   => $th->getMessage(),
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Delete one order record given the id
   * @return \Illuminate\Http\JsonResponse
   */
  public function Destroy(string $id) {
    try {
      $order  = Orders::find($id);
      $delete = Orders::destroy($id);
      if ($delete != 0) {
        return response()->json([
          'message' => sprintf('Order was %s deleted', $id),
          'data'    => $order,
        ], Response::HTTP_OK);
      } else {
        return response()->json([
          'message' => sprintf('Order %s was not found.', $id),
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
   * Updates one order record based on the id and request information
   * @return \Illuminate\Http\JsonResponse
   */
  public function Update(string $id, UpdateOrdersRequest $request) {
    try {
      $order = Orders::find($id);
      if ($order) {
        $order->update($request->all());
        return response()->json($order, Response::HTTP_OK);
      } else {
        return response()->json([
          'message' => sprintf('Order %s not found.', $id),
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
   * Performs a query search for orders based on the provided criteria.
   * @return \Illuminate\Http\JsonResponse
   */
  public function Search(Request $request) {
    try {
      $filter = new OrdersQuery();
      $query  = $filter->Transform($request);
      $orders = Orders::where($query)->paginate();
      if (count($orders->all()) > 0) {
        return response()->json($orders, Response::HTTP_OK);
      } else {
        return response()->json([
          'message' => 'Nothing found.',
        ], Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Something went wrong.',
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  #endregion
}
