<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chart;
use Illuminate\Support\Facades\Http;

class ChartController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }

    public function getContainerCount()
    {
        return 20;
    }

    public function getStatToday() {
        $dateNow = date('Y-m-d');
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateNow,
            "end_date" => $dateNow,
            "type" => "get_in"
        ]);
        if($response->successful()) {
            $dataRes["container_in"] = $response["data"]["count"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateNow,
            "end_date" => $dateNow,
            "type" => "get_out"
        ]);
        if($response->successful()) {
            $dataRes["container_out"] = $response["data"]["count"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateNow,
            "end_date" => $dateNow,
            "type" => "invoiced"
        ]);
        if($response->successful()) {
            $dataRes["invoice"] = $response["data"]["count"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
        return $dataRes;
    }

    public function getDataChartContainerMonth() {
        $dateNow = date('Y-m-d');
        $dateMin30 = date('Y-m-d', strtotime("-30 days"));
        $dataRes = [];
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateMin30,
            "end_date" => $dateNow,
            "type" => "get_in"
        ]);
        if($response->successful()) {
            $dataRes["container_in"] = $response["data"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateMin30,
            "end_date" => $dateNow,
            "type" => "get_out"
        ]);
        if($response->successful()) {
            $dataRes["container_out"] = $response["data"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
        return $dataRes;
    }

    public function getDataChartInvoiceMonth() {
        $dateNow = date('Y-m-d');
        $dateMin30 = date('Y-m-d', strtotime("-30 days"));
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'statistic/container_in_out_by_date', [
            "start_date" => $dateMin30,
            "end_date" => $dateNow,
            "type" => "invoiced"
        ]);
        if($response->successful()) {
            return $response["data"];
        } else {
            return response()->json([
                'message'=>'error'
            ], 400);
        }
    }
}