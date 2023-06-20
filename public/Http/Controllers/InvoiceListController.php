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

class InvoiceListController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function datalist(Request $request) 
    {   
        return view('fitur.invoicelist');
    }

    public function invoiceList(Request $request) 
    {   
        
        /*Log Request*/
        $http_type = "GET";
        $service = "INVOICELIST";
        $feature = "INVOICE";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($this->url . 'inv/get_invoice_all', [
                "page" => $request->start,
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
                $actionBtn = '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["invoice_id"].'">Delete</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function deleteInvoice(Request $request) {
        $id = $request->id;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETEINVOICE";
        $feature = "INVOICE";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($this->url.'inv/delete_invoice_by_id?no_invoice='.$id);
        
        if($response->successful()) {
            return redirect()->route('datalist')->with("success", "Data deleted successfully");
        } else {
            return redirect()->route('datalist')->with("error", "gagal hapus data. ".$response["msg"]);
        }
    }

}