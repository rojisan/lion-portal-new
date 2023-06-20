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

class HargaController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function harga(Request $request) 
    {    
        $custcode = '';  

        /*Log Request*/
        $http_type = "GET";
        $service = "HARGA(INDEX)";
        $feature = "HARGA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        /* Get Customer */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'cust/get_all_customer?limit=1000');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $custdata = $resp['data'];
            $custdatatArray = [];

            foreach ($custdata as $key => $value) {
                array_push($custdatatArray, [
                    "NAME" => trim($value['customer_nama'])
                ]);
            }
            $data['custcode'] = $custdatatArray; 
        }  

        return view('fitur.harga', $data);
        
    }

    public function hargaList(Request $request) 
    {   
        /*Log Request*/
        $http_type = "GET";
        $service = "HARGALIST";
        $feature = "HARGA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'harga/get_harga_all', [
                "start" => $request->start,
                "limit" => $request->length
            ]);
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp['data'];
        } else {
            $data = $resp['detail'];
        }
        return DataTables::of($data)
            ->addColumn('action', function($row){
                $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
                data-id="'.$row["h_id"].'" data-type="'.$row["h_type"].'" data-size="'.$row["h_size"].'" data-customer="'.$row["h_customer"].'" 
                data-storage="'.$row["h_storage_per_day"].'" data-lolo="'.$row["h_lolo"].'" data-shifting="'.$row["h_shifting"].'" data-plugging="'.$row["h_plugging"].'" data-ctt="'.$row["h_cont_to_truck"].'" data-ctc="'.$row["h_cont_to_cont"].'" data-dg="'.$row["h_dg"].'">Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["h_id"].'" data-type="'.$row["h_type"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function hargaListJson(Request $request) {
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'cust/get_all_customer');
        $resp = json_decode($response, true);
        
        if($response->successful()) {
            $data = $resp;
        } else {
            $data = $resp['detail'];
        }
        return $data;
    }

    public function addHarga(Request $request)
    {
        $h_type = $request->type;
        $h_dg = $request->statusdg;
        $h_size = $request->size;
        $h_customer = $request->customer;
        
        $exp_perhari= explode(",", $request->harga_storage_perhari);
        $h_storage_per_day = implode($exp_perhari);

        $exp_lolo = explode(",", $request->harga_lolo);
        $h_lolo = implode($exp_lolo);

        $exp_shifting = explode(",",  $request->harga_shifting);
        $h_shifting = implode($exp_shifting);

        $exp_plugging = explode(",", $request->harga_plugging);
        $h_plugging = implode($exp_plugging);

        $exp_cont_to_truck = explode(",", $request->h_cont_to_truck);
        $h_cont_to_truck = implode($exp_cont_to_truck);

        $exp_cont_to_cont = explode(",",  $request->h_cont_to_cont);
        $h_cont_to_cont = implode($exp_cont_to_cont);
        
     
        /*Log Request*/
        $http_type = "GET";
        $service = "ADDHARGA";
        $feature = "HARGA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'harga/insert_harga', [ 
            "h_type" => $h_type,
            "h_dg" => $h_dg,
            "h_size" => $h_size,
            "h_customer" => $h_customer,
            "h_storage_per_day" => $h_storage_per_day,
            "h_lolo" => $h_lolo,
            "h_shifting" => $h_shifting,
            "h_plugging" => $h_plugging,
            "h_cont_to_truck" => $h_cont_to_truck,
            "h_cont_to_cont" => $h_cont_to_cont,
        ]);
        
        if($response->successful()) {
            return redirect()->route('harga')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        
        return redirect()->route('harga')->with("error", "gagal input data. ".$response["msg"]);
    }

    public function deleteHarga(Request $request) {
        $id = $request->h_id;
       
        /*Log Request*/
        $http_type = "GET";
        $service = "DELETEHARGA";
        $feature = "HARGA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'harga/delete_harga?h_id='.$id);
        
        if($response->successful()) {
            return redirect()->route('harga')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('harga')->with("error", "gagal hapus data. ".$response["detail"]["0"]["msg"]);
        }
    }

    public function updateHarga(Request $request) {
        $data = $request->except('_token');
        
        /*Log Request*/
        $http_type = "GET";
        $service = "UPDATEHARGA";
        $feature = "HARGA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'harga/update_harga', $data);
       
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('harga')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('harga')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('harga')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
    }
    
}