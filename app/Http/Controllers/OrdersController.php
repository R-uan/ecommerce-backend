<?php

namespace App\Http\Controllers;

use App\Models\OrderItens;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller {
    #region Authenticated Client Function
    /**
     * Makes a new order
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
                    return response()->json(
                        [
                            'response'            => 'One of the products is not available',
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
            return response()->json(['response' => $order], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Ocorreu um erro ao processar o pedido' . $th], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Orders From A Costumer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show() {
        $user  = auth()->user();
        $id    = $user->id;
        $query = Orders::where('client_id', $id)->with('orderItens')->get();
        return response()->json(['response' => $query], Response::HTTP_OK);
    }
    #endregion

    #region Administrative Functions
    /**
     * Returns All Orders
     * @return \Illuminate\Http\JsonResponse
     */
    public function all() {
        return response()->json(Orders::all());
    }

    /**
     * Find One Order By Id
     * @return \Illuminate\Http\JsonResponse
     */
    public function one(string $id) {
        try {
            $order = Orders::find($id);
            if ($order) {
                return response()->json(['response' => $order], Response::HTTP_OK);
            } else {
                return response()->json(['response' => sprintf('Order %s not found!', $id)], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return response()->json(['response' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #endregion
}
