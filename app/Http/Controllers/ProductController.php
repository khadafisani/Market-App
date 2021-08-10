<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductUpdate;
use App\Http\Requests\ProductStore;

class ProductController extends Controller
{
    public function store(ProductStore $request)
    {
        $valid = $request->validated();

        $data = [
            'name'  => $request->name,
            'stock' => $request->stock,
            'price' => $request->price,
        ];

        Product::create($data);

        return response()->json([
            'status' => 'ok',
            'message' => 'Product Successfully Saved',
            'code' => 201,
            'data' => [],
        ]);
    }


    public function product()
    {
        $product = Product::all();
        return response()->json([
            'status' => 'ok',
            'message' => null,
            'code' => 200,
            'data' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if($product)
        {
            $product->delete();
            $data = [
                'status' => 'ok',
                'message' => 'Product has been removed',
                'code' => 200,
                'data' => [],
            ];
        }
        else
        {
           $data = [
                'status' => 'failed',
                'message' => 'Product not found',
                'code' => 401,
                'data' => [],
           ];
        }

        return response()->json($data);
    }

    public function update(ProductUpdate $request, $id)
    {
        $valid = $request->validated();

        $product = Product::find($id);
        if($product)
        {
            $product->name  = $request->name;
            $product->stock = $request->stock;
            $product->price = $request->price;

            $product->save();

            $data = [
                'status' => 'ok',
                'message' => 'Product successfully Update',
                'code' => 200,
                'data' => [],
            ];
        }
        else
        {
            $data = [
                'status' => 'ok',
                'message' => 'Product not found',
                'code' => 401,
                'data' => [],
           ];
        }

        return response()->json($data);
    }

    public function specific($id)
    {
        $product = Product::find($id);

        if($product)
        {
            $data = [
                'status' => 'ok',
                'code' => 200,
                'message' => null,
                'data' => $product,
            ];
        }
        else
        {
            $data = [
                'status' => 'failed',
                'code' => 401,
                'message' => 'Product not found',
                'data' => [],
            ];
        }
    }
}
