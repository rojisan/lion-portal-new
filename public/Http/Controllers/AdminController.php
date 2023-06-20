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

class AdminController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function admin(Request $request) 
    {   
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'admin/whoami');

        $usertype =  $response['user_type'];

        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"], $data);
            return redirect()->route('login-page');
        }
        $session = array(
            'user_type' => $usertype
        );
        Session::put('user_type', $usertype);

        return view('fitur.admin');
        
    }

    public function datalist(Request $request) 
    {   
        $search_value = $request->search["value"];
        if($search_value!=null) {
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($this->url . 'admin/get_administrator_by_name', [
                "name"=>$search_value,
                "start"=>$request->start,
                "limit"=>$request->length,
                "order_by"=>"asc adm_name"
            ]);
        } else {
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($this->url . 'admin/get_all_adm', [
                "start"=>$request->start,
                "limit"=>$request->length,
                "order_by"=>"asc adm_name"
            ]);
        }
        $resp = json_decode($response, true);
        if($response->successful()) {
            $data = $resp['data'];
        } else {
            $data =[];
        }
        return DataTables::of($data)
            ->addColumn('action', function($row){
                $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
                data-id="'.$row["adm_id"].'" data-name="'.$row["adm_name"].'" data-email="'.$row["adm_email"].'" 
                data-status="'.$row["deskripsi"].'" data-ws="'.$row["user_type"].'">Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["adm_id"].'" data-name="'.$row["adm_name"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->make(true);
    }

    public function addAdmin(Request $request)
    {
        $data = $request->except('_token');
        $json = json_encode($data);
        if(!$request->get('deskripsi')) {
            $data["deskripsi"] = "deactive";
        }

        /*Log Request*/
        $http_type = "POST";
        $service = "ADDADMIN";
        $feature = "ADMIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'admin/insert_administrator', $data);
        if($response->successful()) {

            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $json);
            return redirect()->route('admin')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $json);
            return redirect()->route('login-page');
        }
        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $json);
        return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
    }

    public function deleteAdmin(Request $request) {
        $id_admin = $request->id;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETEADMIN";
        $feature = "ADMIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'admin/delete_administrator?id='.$id_admin);
        if($response->successful()) {

            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response["msg"], '');
            return redirect()->route('admin')->with("success", "Data deleted successfully");
        } else {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], '');
            return redirect()->route('admin')->with("error", "gagal hapus data. ".$response["msg"]);
        }
    }

    public function updateAdmin(Request $request) {
        $data = $request->except('_token');
        $json = json_encode($data);
        
        if(!$request->get('deskripsi')) {
            $data["deskripsi"] = "deactive";
        }

        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATEADMIN";
        $feature = "ADMIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'admin/update_administrator', $data);
        
        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, $response->status(), $response->status() == 401, $json);
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, $response->status(), $response->status() == 422, $json);
            return redirect()->route('admin')->with("error", "gagal update data. ".$response["msg"]);
        }
        if($response->successful()) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response["msg"], $json);
            return redirect()->route('admin')->with("success", "Data updated successfully");
        } else {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $json);
            return redirect()->route('admin')->with("error", "gagal update data. ".$response["msg"]);
        }
    }
    
}