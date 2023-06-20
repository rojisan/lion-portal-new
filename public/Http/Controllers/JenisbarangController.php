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

class JenisbarangController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function jenisbarang(Request $request) 
    {   
       
        return view('fitur.jenisbarang');
        
    }

    public function jenibarangList(Request $request) 
    {   
        /*Log Request*/
        $http_type = "GET";
        $service = "JENISBARANGLIST";
        $feature = "JENISBARANG";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'barang/get_all_barang', [
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
                data-id="'.$row["id"].'" data-name="'.$row["nama_barang"].'" >Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["id"].'" data-name="'.$row["nama_barang"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function jenisbarangListJson(Request $request) {
        /*Log Request*/
        $http_type = "GET";
        $service = "JENISBARANGLIST";
        $feature = "JENISBARANG";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
  
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'barang/get_all_barang?limit=500');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp;
        } else {
            $data = $resp['detail'];
        }
        return $data;
    }

    public function addjenisbarang(Request $request)
    {
        $name = $request->jenisbarang;
     
        /*Log Request*/
        $http_type = "POST";
        $service = "ADDJENISBARANG";
        $feature = "JENISBARANG";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
  
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'barang/insert', [ 
            "nama_barang" => $name,

        ]);
        if($response->successful()) {
            return redirect()->route('jenisbarang')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        return redirect()->route('jenisbarang')->with("error", "gagal input data. ".$response["detail"]);
    }

    public function deleteBarang(Request $request) {
        $id = $request->id;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETEBARANG";
        $feature = "BARANG";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'barang/delete_barang?id='.$id);
        
        if($response->successful()) {
            return redirect()->route('jenisbarang')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('jenisbarang')->with("error", "gagal hapus data. ".$response["detail"]);
        }
    }

    public function updateBarang(Request $request) {
        $data = $request->except('_token');
        
        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATETBARANG";
        $feature = "BARANG";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'barang/update_barang', $data);
       
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('jenisbarang')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('jenisbarang')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('jenisbarang')->with("error", "gagal update data. ".$response["detail"]);
        }
    }
    
}