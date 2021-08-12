<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Http\Requests\VoucherStore;
use App\Http\Requests\VoucherEdit;

class VoucherController extends Controller
{
    public function store(VoucherStore $request)
    {
        $request->validated();

        Voucher::create($request->all());

        return response()->json([
            'status' => 'ok',
            'code' => 201,
            'message' => "voucher successfully created!",
            'data' => [],
        ]);
    }

    public function destroy($id)
    {
        $voucher = Voucher::find($id);

        if($voucher)
        {
            $voucher->delete();
            $data = [
                'status' => 'ok',
                'code' => 201,
                'message' => "voucher successfully deleted!",
                'data' => [],
            ];
        }
        else
        {
            $data = [
                'status' => 'failed',
                'code' => 401,
                'message' => "voucher not found",
                'data' => [],
            ];
        }

        return response()->json($data);
    }

    public function vouchers()
    {
        return response()->json([
            'status' => 'ok',
            'code' => 201,
            'message' => null,
            'data' => Voucher::all(),
        ]);
    }

    public function edit(VoucherEdit $request, $id)
    {
        $request->validated();

        $voucher = Voucher::find($id);

        if($voucher)
        {
            $voucher->name = $request->name;
            $voucher->point = $request->point;
            $voucher->discount = $request->discount;
            $voucher->max_amount = $request->max_amount;
            $voucher->save();

            $response = [
                'status' => 'ok',
                'code' => 201,
                'message' => 'Voucher Successfully updated!',
                'data' => []
            ];
        }
        else
        {
            $response = [
                'status' => 'failed',
                'code' => 401,
                'message' => 'Voucher not found!',
                'data' => []
            ];
        }

        return response()->json($response);
    }
}
