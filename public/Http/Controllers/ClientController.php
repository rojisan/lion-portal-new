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

class ClientController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function client(Request $request) 
    {   
       
        return view('fitur.client');
        
    }

    public function clientList(Request $request) 
    {   
        
        /*Log Request*/
        $http_type = "GET";
        $service = "CIENTLIST";
        $feature = "CLIENT";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'klien/get_klien_all', [
                "start" => $request->start,
                "limit" => $request->length
            ]);
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
                data-id="'.$row["c_id"].'" data-name="'.$row["c_name"].'"  data-group="'.$row["c_prinsp"].'" data-alamat="'.$row["c_alamat"].'">Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["c_id"].'" data-name="'.$row["c_name"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function ClientListJson(Request $request) {

        /*Log Request*/
        $http_type = "GET";
        $service = "CIENTLISTJSON";
        $feature = "CLIENT";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'klien/get_klien_all');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp;
        } else {
            $data = $resp['detail'];
        }
        return $data;
    }

    public function addClient(Request $request)
    {
        $name = $request->c_name;
        $group = $request->c_prinsp;
        $alamat = $request->alamat;
        
        /*Log Request*/
        $http_type = "POST";
        $service = "ADDCLIENT";
        $feature = "CLIENT";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'klien/insert_klien', [ 
            "c_name" => $name,
            "c_prinsp" => $group,
            "c_alamat" => $alamat,
            "c_last_update" => date('Y-m-d H:i:s')

        ]);
        
        if($response->successful()) {
            return redirect()->route('client')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        return redirect()->route('client')->with("error", "gagal input data. ".$response["detail"]);
    }

    public function deleteClient(Request $request) {
        $id = $request->id;

        /*Log Request*/
        $http_type = "GET";
        $service = "DELETECLIENT";
        $feature = "CLIENT";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'klien/delete_klien?id='.$id);
     
        if($response->successful()) {
            return redirect()->route('client')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('client')->with("error", "gagal hapus data. ".$response["detail"]);
        }
    }

    public function updateClient(Request $request) {
        $data = $request->except('_token');
       
        /*Log Request*/
        $http_type = "GET";
        $service = "UPDATECLIENT";
        $feature = "CLIENT";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'klien/update_klien', $data);
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('client')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('client')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('client')->with("error", "gagal update data. ".$response["detail"]);
        }
    }
    
}