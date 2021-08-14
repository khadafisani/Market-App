<?php

namespace App\Http\Controllers;

use App\Models\ProductOut;
use App\Models\Product;
use App\Http\Requests\ReportByDay;
use App\Http\Requests\ReportStocks;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function byDate(ReportByDay $request)
    {
        $request->validated();
        $report = ProductOut::getReport(); //custom function
        $category = strtolower($request->category); //convert category to lower case to prevent error

        if($category == "day")
        {
            $data = $report->whereDate('product_out.created_at', $request->date)->get();
        }
        else if($category == "month")
        {
            $month = date('m', strtotime($request->date));
            $year = date('Y', strtotime($request->date));
            $data = $report->whereMonth('product_out.created_at', '=', $month)->whereYear('product_out.created_at', '=', $year)->get();
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'code' => 401,
                'message' => 'invalid category',
                'data' => []
            ]);
        }

        $income = 0;
        $outcome = 0;
        //count income and outcome
        foreach($data as $result)
        {
            $income += $result->income;
            $outcome += $result->outcome;
        }

        $dataLaba = [
            'income' => $income,
            'outcome' => $outcome,
            'profit' => $income - $outcome
        ];

        $res = [
            'data' => $data,
            'laba' => $dataLaba
        ];

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => null,
            'data' => $res,
        ]);
    }

    public function stocks(ReportStocks $request)
    {
        $request->validated();

        //set global variabel for custom withCount
        $this->month = date('m', strtotime($request->date));
        $this->year = date('Y', strtotime($request->date));

        $data = Product::withCount(['productOutWithTrashedParent as stock_out' => function($query){
            //sum stock_out by Month and year
            $query->whereMonth('product_out.created_at', '=', $this->month)->whereYear('product_out.created_at', '=', $this->year)->select(DB::raw('sum(stock_out)'));

        }])
        ->withCount(['productInWithTrashed as stock_in' => function($query){
            //sum stock_in by Month and year
            $query->whereMonth('product_in.created_at', '=', $this->month)->whereYear('product_in.created_at', '=', $this->year)->select(DB::raw('sum(stock_in)'));

        }])->get();

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'message' => null,
            'data' => $data,
        ]);
    }
}
