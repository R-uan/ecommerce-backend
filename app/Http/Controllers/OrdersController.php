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
    public function create(Request $request) {
        $userId = auth()->user()->id;
        DB::beginTransaction();
        try {
            $orderInfo = [
                'order_date' => now(),
                'status'     => 'Pending',
                'total'      => null,
                'client_id'  => $userId,
            ];

            $order = new Orders($orderInfo);
            $order->save();

            $totalOrderPrice = 0;
            $products        = $request->products;
            foreach ($products as $order_product) {
                $product = Products::find($order_product['product_id']);
                if ($product && $product->availability == true) {
                    $orderItensInfo = [
                        'orders_id'   => $order->id,
                        'product_id'  => $product->id,
                        'amount'      => $order_product['amount'],
                        'unit_price'  => $product->unit_price,
                        'total_price' => $product->unit_price * $order_product['amount'],
                    ];
                    $orderProduct = new OrderItens($orderItensInfo);
                    $orderProduct->save();
                    $totalOrderPrice += $orderItensInfo['total_price'];
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message'             => 'One of the products is not available',
                        'unavailable_product' => [
                            'product_id'   => $order_product['product_id'],
                            'product_info' => $product ? $product : null,
                        ],
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $order->total = $totalOrderPrice;
            $order->save();

            DB::commit();
            return response()->json(['message' => $order], Response::HTTP_OK);
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
    public function userOrders() {
        try {
            $user  = auth()->user();
            $id    = $user->id;
            $query = Orders::where('client_id', $id)->with('orderItens')->get();
            return response()->json([
                'message' => sprintf('Orders found for user %s', $id),
                'data'    => $query,
            ], Response::HTTP_OK);
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
    public function all() {
        try {
            $orders = Orders::all()->paginate();
            return response()->json([
                'message' => 'Orders found.',
                'data'    => $orders,
            ], Response::HTTP_OK);
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
    public function one(string $id) {
        try {
            $order = Orders::find($id);
            if ($order) {
                return response()->json([
                    'message' => 'Order found',
                    'data'    => $order,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => sprintf('Order %s not found!', $id),
                ], Response::HTTP_OK);
            }
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
    public function destroy(string $id) {
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
    public function update(string $id, UpdateOrdersRequest $request) {
        try {
            $order = Orders::find($id);
            if ($order) {
                $order->update($request->all());
                return response()->json([
                    'message' => 'Order updated',
                    'data'    => $order,
                ], Response::HTTP_OK);
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
    public function search(Request $request) {
        try {
            $filter = new OrdersQuery();
            $query  = $filter->transform($request);
            $orders = Orders::where($query)->paginate();
            if (count($orders->all()) > 0) {
                return response()->json([
                    'message' => 'Orders Found.',
                    'data'    => $orders,
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Nothing found.',
                ], Response::HTTP_NO_CONTENT);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion
}
