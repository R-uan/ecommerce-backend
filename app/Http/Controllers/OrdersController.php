<?php
namespace App\Http\Controllers;

use App\Http\Requests\Update\UpdateOrdersRequest;
use App\Models\OrderItens;
use App\Models\Orders;
use App\Models\PlanetDestination;
use App\Models\Products;
use App\Services\Filters\OrdersQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller {
  /**
   * Creates a new order record under the authenticated client
   * @return \Illuminate\Http\JsonResponse
   */
  public function Create(Request $request) {
    $user_id = auth()->user()->id;
    DB::beginTransaction();
    try {
      $destination        = PlanetDestination::where('id', $request->planet_destination)->exists();
      $initial_order_info = [
        'total'                 => 0,
        'order_date'            => now(),
        'client_id'             => $user_id,
        'status'                => 'Pending',
        'payment_method'        => $request->payment_method,
        'planet_destination_id' => $destination ? $request->planet_destination : throw new \ErrorException("Destination not found."),
      ];

      $unavailable_products = [];
      $order                = new Orders($initial_order_info);
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
          array_push($unavailable_products, [
            'product_id'   => $cart_product['product_id'],
            'product_info' => $product ? $product : null,
          ]);
        }
      }

      if (count($unavailable_products) > 0) {
        DB::rollBack();
        return response()->json([
          'message'              => 'One of more products are not available.',
          'unavailable_products' => $unavailable_products,
        ], Response::HTTP_BAD_REQUEST);
      } else {
        $order->total = $total_price;
        $order->save();
        DB::commit();
        return response()->json($order, Response::HTTP_OK);
      }

    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Retrieves one order record from the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function One(string $id) {
    try {
      $user_id = auth()->user()->id;
      $order   = Orders::where('id', $id)
        ->where('client_id', $user_id)
        ->with([
          'PlanetDestination'                => function ($query) {
            $query->select('id', 'name', 'delivery_price', 'special_conditions');
          },
          'OrderItens.products'              => function ($query) {
            $query->select('id', 'name', 'category', 'image_url', 'manufacturers_id');
          },
          'OrderItens.products.manufacturer' => function ($query) {
            $query->select('id', 'name');
          },
        ])
        ->first();
      return $order ?
      response()->json($order, Response::HTTP_OK) :
      response()->json(sprintf('Order %s not found!', $id), Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Retrieves all orders records from the database
   * @return \Illuminate\Http\JsonResponse
   */
  public function All() {
    try {
      $orders = Orders::all()->paginate();
      return response()->json($orders, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Delete one order record given the id
   * @return \Illuminate\Http\JsonResponse
   */
  public function Destroy(string $id) {
    try {
      $delete = Orders::destroy($id);
      return $delete == 1 ?
      response()->json(sprintf('Order was %s deleted', $id), Response::HTTP_OK) :
      response()->json(sprintf('Order %s was not found.', $id), Response::HTTP_NOT_FOUND);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
        return response()->json(sprintf('Order %s not found.', $id), Response::HTTP_NOT_FOUND);
      }
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
      return response()->json($orders, Response::HTTP_OK);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
