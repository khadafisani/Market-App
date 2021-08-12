<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionGetPoint;
use App\Http\Requests\TransactionStore;
use App\Http\Requests\TransactionIsMember;
use App\Http\Requests\TransactionDoTransaction;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Member;
use App\Models\Voucher;

class TransactionController extends Controller
{
    public function store(TransactionStore $request)
    {
        $request->validated();

        $items = $request->all();

        //create new transaction
        $transaction = new Transaction;

        $transaction->users_id = Auth::user()->id;
        $transaction->save();

        $transactionId = $transaction->id;

        for($i=0; $i<count($items); $i++)
        {
            $product_id = $items['input'][$i]['id']; //barang yang dipesan
            $orderValue = $items['input'][$i]['value']; //jumlah barang yang dipesan

            //mendapatkan total stock per item saat ini
            $product = Product::withSum('productOut as stock_out', 'stock_out')->withSum('productIn as stock_in', 'stock_in')->where('id', $product_id)->get();

            $totalStock = $product[0]->stock_in - $product[0]->stock_out; //dapatkan total stock keseluruhan saat ini

            if($totalStock >= $orderValue) //apakah total stock memenuhi jumlah pesanan
            {
                while($orderValue !=0)
                {
                    $productIn = ProductIn::withSum('productOut as stock_out', 'stock_out')->where('product_id', $product_id)->get()->first(); //dapatkan stock barang yang paling awal

                    $productOut = new ProductOut; //akan ada barang yang keluar
                    $productOut->transaction_id = $transactionId; //barang keluar pada traksasi ID
                    $productOut->product_in_id = $productIn->id; //menggunakan stock masuk ID

                    $stock = $productIn->stock_in - $productIn->stock_out; //dapatkan stock
                    if($stock > $orderValue) //jika stock lebih banyak dari pesanan
                    {
                        $productOut->stock_out = $orderValue; //total barang yang keluar
                        $orderValue = 0; //berarti orderan selesai
                    }
                    else
                    {
                        //jika stock tidak memenuhi jumlah pesanan, maka gunakan stok yang tersisa, dan gunakan stock_in yang lain pada perulangan selanjutnya
                        $productOut->stock_out = $stock; //keluar sebanyak stok tersisa (all in)

                        $orderValue -= $productOut->stock_out; //orderValue pasti > 0 sehingga akan melakukan pengulangan

                        $productIn->delete(); //melakukan softdeletes untuk menandakan bahwa barang habis
                    }
                    $productOut->save();

                    $transaction->total += $productOut->stock_out * $productIn->price; //total per item out
                    $transaction->save();
                }
            }
            else
            {
                return response()->json([
                    'status' => 'failed',
                    'code' => 401,
                    'message' => 'Stock '.$product->name. 'tidak mencukupi, tersisa'.$totalStock,
                ]);
            }
        }

        return response()->json([
            'status' => 'ok',
            'code' => 201,
            'message' => 'Traksaksi telah disimpan',
        ]);
    }

    public function isMember(TransactionIsMember $request)
    {
        $request->validated();

        //Get data member
        $member = Member::find($request->id);

        //get last transaction made by current cashier
        $transaction = Transaction::where('users_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        //insert member ID to transaction
        $transaction->members_id = $request->id;
        $transaction->save();

        //count member point balance with current transaction point
        $member->totalPoint += $this->caltulatePoint($transaction->total);

        //get all voucher
        $voucher = Voucher::all();

        $data = [
            'member' => $member,
            'voucher' => $voucher,
        ];

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => "Member is available",
            'data' => $data,
        ]);
    }

    private function caltulatePoint($totalPrice)
    {
        $point = 0;

        if($totalPrice - 650000 >=0)
        {
            $total = $totalPrice - 650000;
            $point = 50 + floor($total/10000) * 3;
        }
        else if($totalPrice - 300000 >=0)
        {
            $point = 50;
        }
        else if($totalPrice - 100000 >=0)
        {
            $point = 10;
        }

        return $point;
    }

    public function redeemVoucher($id)
    {
        //get last transaction made by current cashier
        $transaction = Transaction::with('members')->where('users_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        $voucher = Voucher::find($id);

        //recalculate member point
        $totalPoint = $transaction->members->totalPoint + $this->calculatePoint($transaction->total);

        if($voucher)
        {
            if($totalPoint < $voucher->point)
            {
                //set voucher id
                $transaction->voucher = $voucher->id;

                //calculate voucher discount
                $discount = $transaction->total * $voucher->discount;

                //if discount*totalTransaction < max amount then $discount, else using voucher max amount
                $totalDiscount = ($discount < $voucher->max_amount) ? $discount : $voucher->max_amount;
                $transaction->total -= $totalDiscount;
                $transaction->save();

                $response = [
                    'status' => 'ok',
                    'code' => 200,
                    'message' => 'Voucher successfully redeemed',
                    'data' => []
                ];
            }
            else
            {
                $response = [
                    'status' => 'ok',
                    'code' => 200,
                    'message' => 'point balance is not sufficient',
                    'data' => []
                ];
            }
        }
        else
        {
            $response = [
                'status' => 'failed',
                'code' => 401,
                'message' => 'Voucher not found',
                'data' => []
            ];
        }

        return response()->json($response);
    }

    public function getTransactionData()
    {
        //get last transaction made by current cashier
        $transaction = Transaction::where('users_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        //get Detail transaction
        $productOut = DB::table('product_out')
        ->join('product_in', 'product_out.product_in_id', '=', 'product_in.id')
        ->join('products', 'products.id', '=', 'product_in.product_id')
        ->where('product_out.transaction_id', '=', $transaction->id)->get();

        $data = [
            'transaction' => $transaction,
            'productOut' => $productOut,
        ];

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => null,
            'data' => $data,
        ]);
    }

    public function doTransaction(TransactionDoTransaction $request)
    {
        $request->validated();

        //get last transaction made by current cashier
        $transaction = Transaction::where('users_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        if($request->pay >= $transaction->total)
        {
            $data = [
                'moneyChanges' => $request->pay - $transaction->total,
            ];

            $response = [
                'status' => 'ok',
                'code' => 200,
                'message' => 'payment success',
                'data' => $data,
            ];
        }
        else
        {
            $response = [
                'status' => 'failed',
                'code' => 200,
                'message' => 'payment failed, money is not sufficient',
                'data' => []
            ];
        }

        return response()->json($response);
    }
}
