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

class BeritaController extends Controller
{
    protected $url;
    public function __construct() {
        $this->url = env("SERVICE");
    }
    public function berita(Request $request) 
    {   
       
        return view('fitur.berita');
        
    }

    public function beritaList(Request $request) 
    {   
        $url = env('SERVICE');
        
        /*Log Request*/
        $http_type = "GET";
        $service = "BERITALIST";
        $feature = "BERITA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'ba/get_ba_all', [
                "start" => $request->start,
                "limit" => $request->length
            ]);
        $resp = json_decode($response, true);
        if($response->successful()) 
        {   
            $data = $resp['data'];
        } else {
            $data = $resp['detail'];
        }
        return DataTables::of($data)
            ->addColumn('action', function($row){
                $actionBtn = '<a href="javascript:void(0)" class="berita btn btn-success btn-sm" 
                data-ct-no="'.$row["ct_no"].'" data-id="'.$row["bm_id"].'" data-name="'.$row["ba_petugas"].'"  data-tempat="'.$row["ba_tempat"].'" data-keterangan="'.$row["ba_keterangan"].'" data-berita="'.$row["ba_nomor_berita"].'" data-status="'.$row["status"].'">Buat Berita</a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function updateBerita(Request $request)
    {
        $url = env('SERVICE');
    
        $noberita = $request->noberita;
        $tempat = $request->tempat;
        $keterangan = $request->keterangan;
        $usertype = Session::get('user_type');

        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATEBERITA";
        $feature = "BERITA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
        
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'container/get_container_get_in');
        
        $resp = json_decode($response, true);
     
        if($response->successful()) 
        {
            $data = $resp['data'];
            $ctno = $data->ct_no;

            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->patch($url . 'ba/update_ba', [
                'ba_nomor_berita' => $noberita,
                'ba_tempat' => $tempat,
                'ba_tanggal_berita' => date('Y-m-d H:i:s'),
                'ba_petugas' => $usertype,
                'ba_keterangan' => $keterangan,
                'ct_no' => $ctno,
                'bm_id' => $data->bm_id
         
            ]);
            if($response->successful()) {
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
                return redirect()->route('index')->with("success", "Data Updated successfully");
            }
    
            if($response->status() == 401) {
                return redirect()->route('login-page');
            }
    
            return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);

        } else {
            $data = [];

            return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
            
        }  
       
    }

    public function generateBerita(Request $request)
    {
        $url = env('SERVICE');
    
        $noberita = $request->noberita;
        $tempat = $request->tempat;
        $keterangan = $request->keterangan;
        $bm_id = $request->bm_id;
        $ct_no = $request->ct_no;
        $status = $request->status;
        $usertype = Session::get('user_type');
        /*Log Request*/
        $http_type = "GET & PATCH";
        $service = "GENERATEBERITA";
        $feature = "BERITA";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($url . 'ba/update_ba', [
            'ba_nomor_berita' => $noberita,
            'ba_tempat' => $tempat,
            'ba_tanggal_berita' => date('Y-m-d H:i:s'),
            'ba_keterangan' => $keterangan,
            'ct_no' => $ct_no,
            'bm_id' => $bm_id,
            'status' => $status
        ]);
        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
        if($response->successful()) 
        {
            
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'ba/generate_ba?bm_id='.$bm_id);
            
            if($response->successful()) {
                $filename = "files/berita/$bm_id.xlsx";
                file_put_contents($filename, $response->getBody()->getContents());
                // return $response->getBody()->getContents();
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
                return redirect()->back()->with("bon_muaturl", $filename);
            }
            if($response->status() == 401) {
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
                return redirect()->route('login-page');
            }
            
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
            return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
            
        } else {
            $data = [];
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
            return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
        }
    }
}