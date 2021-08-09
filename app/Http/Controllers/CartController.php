<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $data = [
            'product_id'    => $request->product_id,
            'user_id'       => Auth::user()->id,
        ];

        $cart = Cart::where($data);
        if($cart)
        {
            $cart->total = $request->total + 1;

            $cart->save();
        }
        else
        {
            Cart::create($data);
        }

        return response()->json(['message' => 'Product has added to cart'], 200);
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
}
