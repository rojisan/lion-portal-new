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

class TruckingController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function trucking(Request $request) 
    {   
       
        return view('fitur.trucking');
        
    }

    public function truckingList(Request $request) 
    {   
        /*Log Request*/
        $http_type = "GET";
        $service = "TRUCKINGLIST";
        $feature = "TRUCKING";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'trucking/get_trucking_all?limit=500');
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
                data-id="'.$row["id"].'" data-name="'.$row["trucking_nama"].'" >Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["id"].'" data-name="'.$row["trucking_nama"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function trcukingListJson(Request $request) {
        /*Log Request*/
        $http_type = "GET";
        $service = "TRUCKINGLISTJSON";
        $feature = "TRUCKING";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
  
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'trucking/get_trucking_all');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp;
        } else {
            $data = $resp['detail'];
        }
        return $data;
    }

    public function addtrucking(Request $request)
    {
        $name = $request->trucking_nama;
     
        /*Log Request*/
        $http_type = "POST";
        $service = "ADDTRUCKING";
        $feature = "TRUCKING";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
  
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'trucking/insert_trucking', [ 
            "trucking_nama" => $name,
            "d_last_update" => date('Y-m-d H:i:s')

        ]);
        if($response->successful()) {
            return redirect()->route('trucking')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        return redirect()->route('trucking')->with("error", "gagal input data. ".$response["detail"]);
    }

    public function deleteTrucking(Request $request) {
        $name = $request->name;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETETRUCKING";
        $feature = "TRUCKING";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'trucking/delete_trucking?nama='.$name);
            
        if($response->successful()) {
            return redirect()->route('trucking')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('trucking')->with("error", "gagal hapus data. ".$response["detail"]);
        }
    }

    public function updateTrucking(Request $request) {
        $data = $request->except('_token');
        
        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATETRUCKING";
        $feature = "TRUCKING";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'trucking/update_trucking', $data);
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('trucking')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('trucking')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('trucking')->with("error", "gagal update data. ".$response["detail"]);
        }
    }
    
}