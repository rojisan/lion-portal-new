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
use Storage;

class RekapController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function rekapdata(Request $request) 
    {   
        $custcode = '';  
        $decode = '';

        /*Log Request*/
        $http_type = "GET";
        $service = "REKAPDATA";
        $feature = "REKAPDATA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        /* Get Customer */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'cust/get_all_customer');
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

        /* Get Depo */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'depo/get_depo_all');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $depodata = $resp['data'];
            $depodatatArray = [];

            foreach ($depodata as $key => $value) {
                array_push($depodatatArray, [
                    "NAME" => trim($value['depo_nama'])
                ]);
            }
            $data['decode'] = $depodatatArray; 
        }  

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, '', $response, $request);
        return view('fitur.rekapdata', $data);
    }

    public function getRekapData(Request $request) {
        $date_arr = $request->get('date_range');
        $start_date = explode(' - ',$date_arr)[0];
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = explode(' - ',$date_arr)[1];
        $end_date = date("Y-m-d", strtotime($end_date));
        $customer = $request->get('customer');
        $type = $request->get('type');
        $tps_asal = $request->get('tpsasal');
        

        /*Log Request*/
        $http_type = "GET";
        $service = "GETREKAPDATA";
        $feature = "REKAP";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->get($this->url . 'container/get_rekap_container', [
            'container_status'=>join(",",$request->get('data_status')),
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            "page"=>$request->start,
            "length"=>10,
            "customer_name"=>$customer,
            "type"=>$type,
            "tps_asal"=>$tps_asal,
        ]);
        
        $resp = json_decode($response, true);

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, '', $response, $request);
        if($response->successful()) 
        {   
            $data = $resp['data'];
        } else {
            $data = [];
        }
        return DataTables::of($data)
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function downloadRekapData(Request $request) {
        $date_arr = $request->get('date_range');
        $start_date = explode(' - ',$date_arr)[0];
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = explode(' - ',$date_arr)[1];
        $end_date = date("Y-m-d", strtotime($end_date));
        $customer = $request->get('customer');
        $type = $request->get('type');
        $tps_asal = $request->get('tpsasal');
        

        /*Log Request*/
        $http_type = "GET";
        $service = "GETREKAPDATA";
        $feature = "REKAP";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->get($this->url . 'container/export_to_excel', [
            'container_status'=>join(",",$request->get('data_status')),
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            "customer_name"=>$customer,
            "type"=>$type,
            "tps_asal"=>$tps_asal,
        ]);
        
        $resp = json_decode($response, true);

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, '', $response, $request);
        if($response->successful()) 
        {   
            $filename = "files/document/Report_Container_out_".$start_date."_".$end_date.".xlsx";
            $doc = file_put_contents($filename, $response->getBody());
            Storage::put($filename, $doc);
            // return $response->getBody()->getContents();
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return url($filename);
        } else {
            $data = [];
        }
    }

}