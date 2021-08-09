<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name'      => 'required|string',
            'stock'     => 'required|integer',
            'price'      => 'required|integer',
        ]);

        if($valid->fails())
        {
            return response()->json($valid->errors(), 422); //422 error response
        }

        $data = [
            'name'  => $request->name,
            'stock' => $request->stock,
            'price' => $request->price,
        ];

        Product::create($data);

        return response()->json(['message' => 'Product Successfully Saved']);
    }


    public function getData(Request $request)
    {
        $product = Product::all();
        return response()->json(['data' => $product], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if($product)
        {
            $product->delete();
            return response()->json(['message' => 'Product has been removed'], 200);
        }
        else
        {
            return response()->json(['message' => 'Product not found'], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'name'      => 'required|string',
            'stock'     => 'required|integer',
            'price'     => 'required|integer',
        ]);

        if($valid->fails())
        {
            return response()->json($valid->errors(), 422); //422 error response
        }

        $product = Product::find($id);
        if($product)
        {
            $product->name  = $request->name;
            $product->stock = $request->stock;
            $product->price = $request->price;

            $product->save();

            return response()->json(['message' => 'Product successfully Update'], 200);
        }
        else
        {
            return response()->json(['message' => 'Product not found'], 400);
        }
    }
}
