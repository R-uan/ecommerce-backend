<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductsRequest;
use App\Models\Products;
use App\Services\ProductsQuery;
use Illuminate\Http\Request;

class ProductsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        return Products::join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer_name')
            ->get();
    }

    public function search(Request $request) {
        $filter = new ProductsQuery();
        $query  = $filter->transform($request);
        return Products::where($query)
            ->join('manufacturers', 'products.manufacturers_id', '=', 'manufacturers.id')
            ->select('products.*', 'manufacturers.name as manufacturer')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        return Products::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductsRequest $request, Products $products) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        return Products::destroy($id);
    }
}
