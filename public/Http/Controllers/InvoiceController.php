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

class InvoiceController extends Controller
{

    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }

    public function invoice(Request $request) 
    {   
        $custcode = '';  

        /*Log Request*/
        $http_type = "GET";
        $service = "INVOICE";
        $feature = "INVOICE";
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

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'INFO', $response, $request);
        return view('fitur.invoice', $data);
    }

    public function getInvoice(Request $request) {
        $date_arr = $request->get('date_range');
        $start = explode(' - ',$date_arr)[0];
        $start_date = date("Y-m-d", strtotime($start));
        $end = explode(' - ',$date_arr)[1];
        $end_date = date("Y-m-d", strtotime($end));
        $customer = $request->get('customer');
        $type = $request->get('type');
        $statusdg = $request->get('statusdg');
       
        /*Log Request*/
        $http_type = "GET";
        $service = "GETINVOICE";
        $feature = "INVOICE";
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
            "length"=>$request->length,
            "customer_name"=>$customer,
            "type"=>$type,
            "status_dg"=>$statusdg,
        ]);
        
        $resp = json_decode($response, true);
        
        if($response->successful()) 
        {   
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'INFO', $response, $request);
            $data = $resp['data'];
        } else {
            $data = [];
        }
        return DataTables::of($data)
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function generateInvoice(Request $request) {
        $noinv = $request->noinvoice;
        $bm_id = $request->bm_id;
        $customer = $request->customer;
        $type = $request->type;
        $template = $request->template;
        $statusdg = $request->statusdg;

        /*Log Request*/
        $http_type = "POST";
        $service = "GENERATEINVOICE";
        $feature = "INVOICE";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $dataHitung = [
            'no_invoice'=> 'No.'. $noinv . '/' .'ISL-TAG'. '/' .date('M-Y'),
            'bm_id'=> $bm_id,
            'customer_name'=> $customer,
            'tanggal_hitung' => date('Y-m-d H:i:s'),
            'inv_type' => $type,
            'inv_template' => $template,
            "status_dg"=>$statusdg,
        ];

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'inv/hitung_invoice', $dataHitung);

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'INFO', $response, $request);
        return $response;
    }

    public function checkedInvoice(Request $request) {
        $noinv = $request->noinvoice;
        $bm_id = $request->bm_id;
        $customer = $request->customer;
        $type = $request->type;
        $template = $request->template;
        $statusdg = $request->statusdg;

        /*Log Request*/
        $http_type = "POST";
        $service = "CHECKEDINVOICE";
        $feature = "INVOICE";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $dataHitung = [
            'no_invoice'=> 'No.'. $noinv . '/' .'ISL-TAG'. '/' .date('M-Y'),
            'bm_id'=> $bm_id,
            'customer_name'=> $customer,
            'tanggal_hitung' => date('Y-m-d H:i:s'),
            'inv_type' => $type,
            'inv_template' => $template,
            'status_dg' => $statusdg
        ];

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($this->url . 'inv/check_invoice', $dataHitung);

        Isl_log::logResponse($http_type, 'INFO', $service, $feature, $userid, 'INFO', $response, $request);
        return $response;
    }

    public function randomExternalid() {
        $alphabet = '';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 3; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}