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

class CustomerController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function customer(Request $request) 
    {   
       
        return view('fitur.customer');
        
    }

    public function customerList(Request $request) 
    {   
        
        /*Log Request*/
        $http_type = "GET";
        $service = "CUSTOMERLIST";
        $feature = "CUSTOMER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'cust/get_all_customer',[
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
                data-id="'.$row["id"].'" data-name="'.$row["customer_nama"].'" data-status="'.$row["status_premium"].'" data-admincontainer="'.$row["biaya_adm_per_container"].'" 
                data-adminvoice="'.$row["biaya_adm_per_invoice"].'" data-materai="'.$row["biaya_materai"].'" data-ppn="'.$row["ppn"].'" data-pic="'.$row["pic_customer"].'" data-alamat="'.$row["alamat"].'" data-cust="'.$row["customer"].'">Edit</a>
                <a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["id"].'" data-name="'.$row["customer_nama"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function customerListJson(Request $request) {
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

    public function addCustomer(Request $request)
    {
        $name = $request->customer_nama;
        $cust = $request->customer;
        $pic = $request->pic;
        $status = $request->status;
        $alamat = $request->alamat;

        $exp_adm_container= explode(",", $request->harga_container);
        $adm_container = implode($exp_adm_container);

        $exp_adm_invoice= explode(",", $request->harga_invoice);
        $adm_invoice = implode($exp_adm_invoice);

        $exp_adm_materai= explode(",", $request->materai);
        $adm_materai = implode($exp_adm_materai);

        $ppn = $request->ppn;
     
        /*Log Request*/
        $http_type = "POST";
        $service = "ADDCUSTOMER";
        $feature = "CUSTOMER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'cust/insert_customer', [ 
            "customer_nama" => $name,
            "customer" => $cust,
            "pic_customer" => $pic,
            "alamat" => $alamat,
            "status_premium" => $status,
            "biaya_adm_per_container" => $adm_container,
            "biaya_adm_per_invoice" => $adm_invoice,
            "biaya_materai" => $adm_materai,
            "ppn" => $ppn,
            "d_last_update" => date('Y-m-d H:i:s')
        ]);
        if($response->successful()) {
            return redirect()->route('customer')->with("success", "Data inserted successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        return redirect()->route('customer')->with("error", "gagal input data. ".$response["detail"]);
    }

    public function deleteCustomer(Request $request) {
        $name = $request->name;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETECUSTOMER";
        $feature = "CUSTOMER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'cust/delete_cust?nama='.$name);
        
        if($response->successful()) {
            return redirect()->route('customer')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('customer')->with("error", "gagal hapus data. ".$response["detail"]);
        }
    }

    public function updateCustomer(Request $request) {
        $data = $request->except('_token');

        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATECUSTOMER";
        $feature = "CUSTOMER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($this->url . 'cust/update_customer', $data);
       
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }
        if($response->status() == 422) {
            return redirect()->route('customer')->with("error", "gagal update data. ".$response["detail"]["0"]["msg"]);
        }
        if($response->successful()) {
            return redirect()->route('customer')->with("success", "Data updated successfully");
        } else {
            return redirect()->route('customer')->with("error", "gagal update data. ".$response["detail"]);
        }
    }
    
}