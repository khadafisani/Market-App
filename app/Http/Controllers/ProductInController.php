<?php

namespace App\Http\Controllers;

use App\Models\ProductIn;
use App\Http\Requests\ProductInStore;

class ProductInController extends Controller
{
    public function store(ProductInStore $request)
    {
        $request->validated();

        ProductIn::create($request->all());

        return response()->json([
            'status' => 'ok',
            'code' => 201,
            'message' => 'Product Stock successfully insert',
            'data' => []
        ]);
    }

    public function destroy($id)
    {
        $productIn = ProductIn::find($id);

        if($productIn)
        {
            $productIn->forceDelete();

            $data = [
                'status' => 'ok',
                'code' => 200,
                'message' => 'Product stock has been deleted',
                'data' => [],
            ];
        }
        else
        {
            $data = [
                'status' => 'failed',
                'code' => 401,
                'message' => 'Stock not found ',
                'data' => [],
            ];
        }

        return response()->json($data);
    }
}
