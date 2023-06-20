<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Auth;
use DataTables;
use App\Helpers\Isl_log;

class DepoController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function depo(Request $request) 
    {   
       
        return view('fitur.depo');
        
    }

    public function depoList(Request $request) 
    {   
        
        /*Log Request*/
        $http_type = "GET";
        $service = "DEPOLIST";
        $feature = "DEPO";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'depo/get_depo_all?limit=500');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp['data'];
            $total = $resp['total'];
        } else {
            $data = $resp['detail'];
        }
        return DataTables::of($data)
            ->addColumn('action', function($row){
                $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
                data-id="'.$row["id"].'" data-name="'.$row["depo_nama"].'" >Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["id"].'" data-name="'.$row["depo_nama"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function depoListJson(Request $request) {
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'depo/get_depo_all');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp;
        } else {
            $data = $resp['detail'];
        }
        return $data;
    }

    public function addDepo(Request $request)
    {
        $name = $request->depo_nama;
        
        /*Log Request*/
        $http_type = "POST";
        $service = "ADDDEPO";
        $feature = "DEPO";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'depo/insert_depo', [ 
            "depo_nama" => $name,
            "d_last_update" => date('Y-m-d H:i:s')

        ]);
        
        if($response->successful()) {
            return redirect()->route('depo')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        return redirect()->route('depo')->with("error", "gagal input data. ".$response["detail"]);
    }

    public function deleteDepo(Request $request) {
        $name = $request->name;

        /*Log Request*/
        $http_type = "GET";
        $service = "DELETEDEPO";
        $feature = "DEPO";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'depo/delete_depo?nama='.$name);
            
        if($response->successful()) {
            return redirect()->route('depo')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('depo')->with("error", "gagal hapus data. ".$response["detail"]);
        }
    }

    public function updateDepo(Request $request) {
        $data = $request->except('_token');
        
        /*Log Request*/
        $http_type = "GET";
        $service = "UPDATEDEPO";
        $feature = "DEPO";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'depo/update_depo', $data);
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('depo')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('depo')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('depo')->with("error", "gagal update data. ".$response["detail"]);
        }
    }
    
}