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
use Imagick;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContainerController extends Controller
{
    public function index() 
    {   
        $url = env('SERVICE');

        $cacc = '';
        $decode = '';
        $tcode = '';
        $custcode = '';
        
        /* Get Consigne */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'klien/get_klien_all?limit=1000' );
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $clientaccount = $resp['data'];
            $clientaccountArray = [];

            foreach ($clientaccount as $key => $value) {
                array_push($clientaccountArray, [
                    "NAME" => trim($value['c_name'])
                ]);
            }
            $data['cacc'] = $clientaccountArray; 
        }  

        /* Get Depo */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'depo/get_depo_all');
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

        /* Get Trucking */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'trucking/get_trucking_all');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $truckingdata = $resp['data'];
            $truckingdatatArray = [];

            foreach ($truckingdata as $key => $value) {
                array_push($truckingdatatArray, [
                    "NAME" => trim($value['trucking_nama'])
                ]);
            }
            $data['tcode'] = $truckingdatatArray; 
        } else {
            return redirect()->back();
        }  

        /* Get Customer */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'cust/get_all_customer');
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

        return view('fitur.container', $data);
        
    }

    public function containerout() 
    {   
        $url = env('SERVICE');

        $cacc = '';
        $decode = '';
        $tcode = '';
        $custcode = '';
        
        /* Get Consigne */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'klien/get_klien_all?limit=1000' );
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $clientaccount = $resp['data'];
            $clientaccountArray = [];

            foreach ($clientaccount as $key => $value) {
                array_push($clientaccountArray, [
                    "NAME" => trim($value['c_name'])
                ]);
            }
            $data['cacc'] = $clientaccountArray; 
        }  

        /* Get Depo */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'depo/get_depo_all');
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

        /* Get Trucking */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'trucking/get_trucking_all');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $truckingdata = $resp['data'];
            $truckingdatatArray = [];

            foreach ($truckingdata as $key => $value) {
                array_push($truckingdatatArray, [
                    "NAME" => trim($value['trucking_nama'])
                ]);
            }
            $data['tcode'] = $truckingdatatArray; 
        } else {
            return redirect()->back();
        }  

        /* Get Customer */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'cust/get_all_customer');
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

        return view('fitur.containerout', $data);
        
    }

    public function viewform() 
    {   
        return view('fitur.form_container'); 
    }

    public function datacontainer(Request $request) 
    {   
        $url = env('SERVICE');
        $usertype = Session::get('user_type');  
        $customer = Session::get('adm_name');
        /* Searching */
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];
        $from_func = "";
        if($searchValue != null || $searchValue != '') {
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'container/search_container_by_no?ct_no=' .$searchValue);
            $from_func = "search first";
        } 

        if($usertype == 'customer'){
            if($searchValue != null || $searchValue != '') {
                $response = Http::withHeaders([
                    "Accept"=>"application/json",
                    "Authorization"=>"Bearer ".session('token'),
                ])->asForm()->get($url . 'container/search_container_by_no', [
                    'ct_no' => $searchValue,
                    'customer_nama' => $customer,
                    "page"=>$request->start,
                    "length"=>$request->length,
                ]);
                Log::info($response);
                $from_func = "search second";
            } else {
                $response = Http::withHeaders([
                    "Accept"=>"application/json",
                    "Authorization"=>"Bearer ".session('token'),
                ])->asForm()->get($url . 'container/get_rekap_container', [
                    'container_status'=>'get_in',
                    'start_date'=>date('Y-m-d H:i:s' , strtotime("-365 days")),
                    'end_date'=>date('Y-m-d H:i:s'),
                    "page"=>$request->start,
                    "length"=>$request->length,
                    "customer_name"=>$customer,
                ]);
                $from_func = "search third";
            }
        } else {
            if($searchValue != null || $searchValue != '') {
                $response = Http::withHeaders([
                    "Accept"=>"application/json",
                    "Authorization"=>"Bearer ".session('token'),
                ])->asForm()->get($url . 'container/search_container_by_no', [
                    'ct_no' => $searchValue,
                    'customer_nama' => $customer
                ]);
                $from_func = "search fourth";
            } else {
                $response = Http::withHeaders([
                    "Accept"=>"application/json",
                    "Authorization"=>"Bearer ".session('token'),
                ])->asForm()->get($url . 'container/get_container_get_in', [
                    "page" => $request->start,
                    "limit" => $request->length
                ]);
                $from_func = "search fifth";
            }

        }
        Log::error($response->successful());
        $resp = json_decode($response, true);
        if($response->successful()) 
        {   
            $data = $resp['data'];
        } else {
            $data = [];
        }
        Log::error($from_func, $data);
        return DataTables::of($data)
        ->addColumn('action', function($row){
            $usertype = Session::get('user_type');
            $parentBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
            data-id="'.$row["ct_id"].'" data-no="'.$row["ct_no"].'" data-client="'.$row["c_client"].'" data-bl="'.$row["ct_nomor_bl"].'" data-importir="'.$row["ct_importir"].'" 
            data-type="'.$row["ct_type"].'" data-ukuran="'.$row["ct_ukuran"].'" data-dg="'.$row["ct_dg"].'" data-tps="'.$row["ct_tps_asal"].'" data-tglmasuk="'.$row["ct_tanggal_masuk"].'" 
            data-nopolmasuk="'.$row["ct_nopol_masuk"].'" data-trucking="'.$row["ct_trucking"].'" data-tglkeluar="'.$row["ct_tanggal_keluar"].'" data-nopolkeluar="'.$row["ct_nopol_keluar"].'" 
            data-deskripsi="'.$row["ct_deskripsi"].'" data-seal="'.$row["ct_nomor_seal"].'" data-aju="'.$row["ct_aju"].'" data-hari="'.$row["ct_lama_hari"].'" data-ei="'.$row["ct_ei"].'"
            data-typestatus="'.$row["ct_type_2"].'" data-ctdo="'.$row["ct_do"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-desc="'.$row["ct_deskripsi"].'"
            data-depo="'.$row["ct_tps_asal"].'" data-trucking="'.$row["ct_trucking"].'"  data-sopir="'.$row["ct_sopir"].'" data-stripping="'.$row["stripping"].'">Edit</a>';

            if($row["ct_document"] == '' ||  $row["ct_gambar"] == ''){
                $csBtn = $parentBtn.'<button href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'" disabled>Bon Muat</button>';

                $masterBtn = $parentBtn.'<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["ct_id"].'"  data-no="'.$row["ct_no"].'">Delete</a> <button href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'" disabled>Bon Muat</button>';
            } else {
                $csBtn = $parentBtn.'<a href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'">Bon Muat</a>';

                $masterBtn = $parentBtn.'<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["ct_id"].'"  data-no="'.$row["ct_no"].'">Delete</a> <a href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'">Bon Muat</a>';
            }

            if($row["ct_document"] == '' ||  $row["ct_gambar"] == ''){
                $superAdminBtn = $parentBtn.'<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["ct_id"].'"  data-no="'.$row["ct_no"].'">Delete</a>
                <button href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'" disabled>Bon Muat</button>
                <a href="javascript:void(0)" class="upload-photo btn btn-primary btn-sm" 
                data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Foto</a>
                <a href="javascript:void(0)" class="upload-document btn btn-secondary btn-sm" 
                data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Document</a>';
            } else {
                $superAdminBtn = $parentBtn.'<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" 
                data-id="'.$row["ct_id"].'"  data-no="'.$row["ct_no"].'">Delete</a>
                <a href="javascript:void(0)" class="bonmuat btn btn-info btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-gambar="'.$row["ct_gambar"].'" data-document="'.$row["ct_document"].'" data-sopir="'.$row["ct_sopir"].'">Bon Muat</a>
                <a href="javascript:void(0)" class="upload-photo btn btn-primary btn-sm" 
                data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Foto</a>
                <a href="javascript:void(0)" class="upload-document btn btn-secondary btn-sm" 
                data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Document</a>';
            }
            
            $kraniBtn = '<a href="javascript:void(0)" class="upload-photo btn btn-primary btn-sm" 
            data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Foto</a>';

            $verifyBtn = '<a href="javascript:void(0)" class="upload-document btn btn-secondary btn-sm" 
            data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Document</a>';

            $customerBtn = '<i data-container="'.$row["ct_no"].'" class="fas fa-exclamation-circle"></i>';

            if($usertype == 'krani'){
                return $kraniBtn;
            }
            if($usertype == 'admin'){
                return $csBtn;
            }
            if($usertype == 'verify'){
                return $verifyBtn;
            }
            if($usertype == 'customer'){
                return $customerBtn;
            }
            if($usertype == 'master'){
                return $masterBtn;
            }
            return $superAdminBtn;
        })
        ->rawColumns(['action'])
        ->setTotalRecords($response["total"])
        ->setFilteredRecords($response["total"])
        ->skipPaging()
        ->make(true);
        
    }

    public function datacontainerout(Request $request) 
    {   
        $url = env('SERVICE');

        /* Searching */
        $search_arr = $request->search;
        $searchValue = $search_arr['value'];
        
        if($searchValue != null || $searchValue != '') {
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'container/search_container_by_no?cont_status=get_out&ct_no=' .$searchValue);
            
        } else {
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'container/get_container_get_out', [
                "page" => $request->start,
                "limit" => $request->length
            ]);
            
        }
        
        $resp = json_decode($response, true);

        if($response->successful()) 
        {
            $data = $resp['data'];

        } else {
            $data = [];
        }

        return DataTables::of($data)
            ->addColumn('action', function($row) use ($url){
                $usertype = Session::get('user_type');
                // return $row["ct_document"];
                // return $row["ct_no"];
                if($row["ct_document"]!="" || $row["ct_document"]!=NULL) {
                    $document_name = str_replace("storage/", "", $row["ct_document"]);
                    $download_btn = '<a download="'.explode(";",$row["ct_document"])[0].'" href="'.Storage::url(explode(";",$document_name)[0]).'" target="_blank" class="btn btn-default btn-sm" 
                    style="margin-left: 5px">Download Doc</a>';
                } else {
                    $download_btn = '<button href="#" style="margin-left: 5px" disabled="true" class="btn btn-default btn-sm">Download Doc</button>';
                }

                if($row["ct_gambar"]!="" || $row["ct_gambar"]!=NULL) {
                    $downloadphoto_btn = '<a href="javascript:void(0)" class="downloadphoto btn btn-default btn-sm" 
                    data-id="'.$row["ct_id"].'" data-bm="'.$row["bm_id"].'" data-no="'.$row["ct_no"].'" data-gambar="'.$row["ct_gambar"].'">Download Photo</a>';
                } else {
                    $downloadphoto_btn = '<button href="#" style="margin-left: 5px" disabled="true" class="btn btn-warning btn-sm">Download Photo</button>';
                }

                $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
                data-id="'.$row["ct_id"].'" data-no="'.$row["ct_no"].'" data-client="'.$row["c_client"].'" data-bl="'.$row["ct_nomor_bl"].'" data-importir="'.$row["ct_importir"].'" 
                data-type="'.$row["ct_type"].'" data-ukuran="'.$row["ct_ukuran"].'" data-dg="'.$row["ct_dg"].'" data-tps="'.$row["ct_tps_asal"].'" data-tglmasuk="'.$row["ct_tanggal_masuk"].'" 
                data-nopolmasuk="'.$row["ct_nopol_masuk"].'" data-trucking="'.$row["ct_trucking"].'" data-tglkeluar="'.$row["ct_tanggal_keluar"].'" data-nopolkeluar="'.$row["ct_nopol_keluar"].'" 
                data-deskripsi="'.$row["ct_deskripsi"].'" data-seal="'.$row["ct_nomor_seal"].'" data-aju="'.$row["ct_aju"].'" data-hari="'.$row["ct_lama_hari"].'" data-ei="'.$row["ct_ei"].'"
                data-typestatus="'.$row["ct_type_2"].'" data-ctdo="'.$row["ct_do"].'" data-sopir="'.$row["ct_sopir"].'" data-stripping="'.$row["stripping"].'">Edit</a>
                <a href="javascript:void(0)" class="upload-photo btn btn-primary btn-sm" 
                data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Photos</a>
                <a href="javascript:void(0)" class="batalmuat btn btn-dark btn-sm" 
                data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-remark="'.$row["remark_batal_muat"].'">Batal Muat</a>'.$download_btn.' '.$downloadphoto_btn;
                
                // <a href="'.route("container.get.document", $row["ct_id"]).'" target="_blank" class="downloaddoc btn btn-default btn-sm" 
                // data-id="'.$row["ct_id"].'" data-ukuran="'.$row["ct_ukuran"].'" data-container="'.$row["ct_no"].'" data-type="'.$row["ct_type"].'" 
                // data-client="'.$row["c_client"].'" data-bm="'.$row["bm_id"].'" data-remark="'.$row["remark_batal_muat"].'">Download Doc</a>';

                $verifyBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm" 
                data-id="'.$row["ct_id"].'" data-no="'.$row["ct_no"].'" data-client="'.$row["c_client"].'" data-bl="'.$row["ct_nomor_bl"].'" data-importir="'.$row["ct_importir"].'" 
                data-type="'.$row["ct_type"].'" data-ukuran="'.$row["ct_ukuran"].'" data-dg="'.$row["ct_dg"].'" data-tps="'.$row["ct_tps_asal"].'" data-tglmasuk="'.$row["ct_tanggal_masuk"].'" 
                data-nopolmasuk="'.$row["ct_nopol_masuk"].'" data-trucking="'.$row["ct_trucking"].'" data-tglkeluar="'.$row["ct_tanggal_keluar"].'" data-nopolkeluar="'.$row["ct_nopol_keluar"].'" 
                data-deskripsi="'.$row["ct_deskripsi"].'" data-seal="'.$row["ct_nomor_seal"].'" data-aju="'.$row["ct_aju"].'" data-hari="'.$row["ct_lama_hari"].'" data-ei="'.$row["ct_ei"].'"
                data-typestatus="'.$row["ct_type_2"].'" data-ctdo="'.$row["ct_do"].'" data-sopir="'.$row["ct_sopir"].'" data-stripping="'.$row["stripping"].'">Edit</a>';
                
                if($usertype == 'verify'){
                    return $verifyBtn;
                }

                if($usertype == 'krani') {
                    $verifyBtn .= '<a href="javascript:void(0)" class="upload-photo btn btn-primary btn-sm" 
                    data-id="'.$row["ct_id"].'" data-container="'.$row["ct_no"].'" data-bm="'.$row["bm_id"].'">Upload Photos</a>';
                    return $verifyBtn;
                }

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setTotalRecords($response["total"])
            ->skipPaging()
            ->make(true);
    }

    public function addcontainer(Request $request) 
    {   
        $url = env('SERVICE');
        $ei_validation = ["Export", "Import", "Domestik"];
        $request->validate([
            "container"=>"required",
            "nopol"=>"required",
            "ei"=>["required",
                    Rule::in($ei_validation)
                    ],
            "type"=>"required",
            "dg"=>"required",
            "status"=>"required",
            "jenis"=>"required",
            "size"=>"required",
            "bl"=>"required",
            "customer"=>"required",
            "gudang"=>"required",
            "consigne"=>"required",
            "trucking"=>"required",
        ]);
        
        $container = $request->container;
        $ei = $request->ei;
        $type = $request->type;
        $dg = $request->dg;
        $status = $request->status;
        $size = $request->size;
        $closedate = $request->closedate;
        $bl = $request->bl;
        $do = $request->do;
        $consigne = $request->consigne;
        $gudang = $request->gudang;
        $nopol = $request->nopol;
        $datein = $request->datein;
        $jenis = $request->jenis;
        $ct_gambar = $request->ct_gambar;
        $ct_document = $request->ct_document;
        $trucking = $request->trucking;
        $customer = $request->customer;
        $sopir = $request->sopir;

        /*Log Request*/
        $http_type = "POST";
        $service = "ADDCONTAINER";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($url . 'container/container_in', [
			'ct_no' => $container,
			'ct_ei' => $ei,
            'ct_type' => $type,
            'ct_type_2' => $status,
            'ct_dg' => $dg,
            'ct_ukuran' => $size,
            'ct_nomor_bl' => $bl,
            'ct_do' => $bl,
            'ct_importir' => $consigne,
            'ct_tps_asal' => $gudang,
            'c_client' => $customer,
            'ct_nopol_masuk' => $nopol,
            'ct_tanggal_masuk' => $datein,
            'ct_trucking' => $trucking,
            'ct_deskripsi' => $jenis,
            'ct_nomor_seal' => 'test',
            'ct_aju' => 'test',
            'ct_rusak' => 'no',
            'ct_gambar'=> $ct_gambar,
            'ct_document'=> 'doc_sementara',
            'customer_nama'=> $customer,
            'ct_sopir' => $sopir,
            'stripping' => '0',
		]);

        $resp = json_decode($response, true);
        if($response->successful()) {
            $ct_no = $resp['data']['ct_no'];
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'acc_bongkar/generate_by_ct_id?ct_id='.$resp["data"]["ct_id"]);
            if($response->successful()) {
                $filename = "files/accbongkar/$ct_no.pdf";
                file_put_contents($filename, $response->getBody()->getContents());
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
                return redirect()->route('index')->with("acc_bongkarurl", $filename);
            }
            
        }      

        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('login-page');
        }
        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
        return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
    }

    public function addform() 
    {   
        $url = env('SERVICE');

        $cacc = '';
        $decode = '';
        $tcode = '';
        $custcode = '';
        $jnscode = '';
        
        /* Get Consigne */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'klien/get_klien_all?limit=1000' );
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $clientaccount = $resp['data'];
            $clientaccountArray = [];

            foreach ($clientaccount as $key => $value) {
                array_push($clientaccountArray, [
                    "NAME" => trim($value['c_name'])
                ]);
            }
            $data['cacc'] = $clientaccountArray; 
        }  

        /* Get Depo */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'depo/get_depo_all');
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

        /* Get Trucking */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'trucking/get_trucking_all');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $truckingdata = $resp['data'];
            $truckingdatatArray = [];

            foreach ($truckingdata as $key => $value) {
                array_push($truckingdatatArray, [
                    "NAME" => trim($value['trucking_nama'])
                ]);
            }
            $data['tcode'] = $truckingdatatArray; 
        } else {
            return redirect()->back();
        }  

        /* Get Customer */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'cust/get_all_customer');
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


        /* Get Jenis Barang */
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'barang/get_all_barang?limit=200');
        $resp = json_decode($response, TRUE);
        if($response->successful()) {
            $jnsdata = $resp['data'];
            $jnsdatatArray = [];

            foreach ($jnsdata as $key => $value) {
                array_push($jnsdatatArray, [
                    "NAME" => trim($value['nama_barang'])
                ]);
            }
            $data['jnscode'] = $jnsdatatArray; 
        }  
        return view('fitur.form_container', $data);
    }

    public function deletecontainer(Request $request) 
    {
        $url = env('SERVICE');
        $id = $request->id;

        /*Log Request*/
        $http_type = "POST";
        $service = "DELETECONTAINER";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->delete($url.'container/container_delete?ct_id='.$id);

        $resp = json_decode($response, true);
        
        if($response->successful()) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return redirect()->back()->with("success", "Data deleted successfully");
        } else {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->back()->with("error", "gagal hapus data. ".$response["detail"][0]["msg"]);
        }
    }

    public function getout(Request $request) 
    {
        $url = env('SERVICE');
       
        $ctid = $request->id;
        $nopol = $request->nopol;
        $dateout = $request->dateout;

        /*Log Request*/
        $http_type = "POST";
        $service = "GETOUT";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($url . 'container/container_out', [
            'ct_id' => $ctid,
            'ct_nopol_keluar' => $nopol,
            'ct_tanggal_keluar' => $dateout
		]);
        
        if($response->successful()) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return redirect()->route('index')->with("success", "successfully");
        }

        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('login-page');
        }

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
        return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
    }

    public function updatecontainer(Request $request) 
    {   
        $url = env('SERVICE');
    
        $id = $request->ct_id;
        $container = $request->container;
        $ei = $request->ei;
        $type = $request->type;
        $dg = $request->dg;
        $status = $request->status;
        $size = $request->size;
        $closedate = $request->closedate;
        $bl = $request->bl;
        $do = $request->do;
        $consigne = $request->consigne;
        $gudang = $request->gudang;
        $mitra = $request->mitra;
        $nopol = $request->nopol;
        $dateout = $request->dateout;
        $ctdo = $request->do;
        $tglmasuk = $request->tglmasuk;
        $ctnopolmasuk = $request->nopolmasuk;
        $jenis = $request->jenis;
        $ct_gambar = $request->ct_gambar;
        $ct_document = $request->ct_document;
        $trucking = $request->trucking;
        $customer = $request->customer;
        $sopir = $request->sopir;
        $striping = $request->striping;

        /*Log Request*/
        $http_type = "POST";
        $service = "UPDATECONTAINER";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($url . 'container/update_container', [
            'ct_id' => $id,
			'ct_no' => $container,
			'ct_ei' => $ei,
            'ct_type' => $type,
            'ct_dg' => $dg,
            'ct_ukuran' => $size,
            'ct_nomor_bl' => $bl,
            'ct_importir' => $consigne,
            'ct_tps_asal' => $gudang,
            'c_client' => $customer,
            'ct_nopol_keluar' => $nopol,
            'ct_tanggal_keluar' => $dateout,
            'ct_trucking' => $trucking,
            'ct_deskripsi' => $jenis,
            'ct_nomor_seal' => 'update',
            'ct_aju' => 'update',
            'ct_do'=> $bl,
            'ct_tanggal_masuk'=> $tglmasuk,
            'ct_nopol_masuk'=> $ctnopolmasuk,
            'ct_rusak'=> '',
            'ct_gambar'=> $ct_gambar,
            'ct_document'=> $ct_document,
            'ct_trucking'=> $trucking,
            'customer_nama'=> $customer,
            'ct_sopir' => $sopir,
            'stripping' => $striping,
		]);
        
        if($response->successful()) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return redirect()->back()->with("success", "Data Updated successfully");
        }

        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('login-page');
        }

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
        return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
    }

    public function bonmuat(Request $request) 
    {
        $url = env('SERVICE');
        
        $idbm = $request->idbm;
        $nopol = $request->nopol;
        $tujuan = $request->tujuan;
        $sopir = $request->sopir;
        $petugas = $request->petugas;
        $dateout = $request->dateout;
        $cetak = $request->cetak;

        $ctid = $request->id;
        $nopol = $request->nopol;

        /*Log Request*/
        $http_type = "POST";
        $service = "BONMUAT";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($url . 'container/container_out', [
            'bm_id' => $idbm,
            'ct_nopol_keluar' => $nopol,
            'ct_tanggal_keluar' => $dateout,
            'bm_tujuan' => $tujuan,
            'bm_sopir' => $sopir,
            'bm_petugas' => $petugas
		]);
        if($response->successful()) { 
            $id = $response["data"]["bm_id"];
            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'bon_muat/generate_by_bm_id?bm_id='.$id);
            if($response->successful()) {
                $filename = "files/bonmuat/$id.pdf";
                file_put_contents($filename, $response->getBody()->getContents());
                // return $response->getBody()->getContents();
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
                return redirect()->route('index')->with("bon_muaturl", $filename);
            }

            if($response->status() == 401) {
                Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
                return redirect()->route('login-page');
            }
            
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('index')->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
    
        } else {

            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('index')->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
        
        }
        if($response->status() == 401) {

            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
            return redirect()->route('login-page');
        }

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["detail"][0]["msg"], $request);
        return redirect()->route('index')->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
    }

    public function batalmuat(Request $request) 
    {
        $url = env('SERVICE');
      
        $idbm = $request->id_bm;
        $remark = $request->remark;
        
        /*Log Request*/
        $http_type = "GET";
        $service = "BATALMUAT";
        $feature = "CONTAINER";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->get($url . 'container/container_reset', [
            'bm_id'=>$idbm,
            'remark'=>$remark,
        ]);
      
        if($response->successful()) { 
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return redirect()->back()->with("success", "Data Batal Muat successfully");

        } else {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
            return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
        }
        if($response->status() == 401) {
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
            return redirect()->route('login-page');
        }

        Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'ERROR', $response["msg"], $request);
        return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
    }

    public function randomExternalid() {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 4; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function donwloadFile($file){
        header("Pragma:public");
        header("Expired:0");
        header("Cache-Control:must-revalidate");
        header("Content-Control:public");
        header("Content-Description: File Download");
        header("Content-Type: pdf");
        header("Content-Disposition:attachment; filename=\"".$file."\"");
        header("Content-Transfer-Encoding:binary");
        header("Content-Length:".filesize($file));
        flush();
        readfile($file);
    }

    public function containerOutUploadPhoto(Request $request)
    {
        $url = env('SERVICE');
        $data = $request->hasFile('images');
        $ct_rusak = $request->get('ct_rusak');
        $usertype = Session::get('user_type');
        $admname = Session::get('adm_name');
        $bm_id = $request->get('bm_id');
        $ct_id = $request->get('upload-image-ct-number');
        /*Log Request*/
        $http_type = "POST";
        $service = "UPLOADIMAGES";
        $feature = "KRANI";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $datenow = new \DateTime();
        $path_list = array();
        $names = [];
        $uploadFiles = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ]);
        if($data) {
            foreach($request->file('images') as $key => $img) {
                // $filename = $datenow->getTimestamp();
                if(file_exists($img)) {
                    $size = filesize($img);
                    if($size > 1024000) {
                        return redirect()->back()->with("error", "Ukuran gambar terlalu besar");
                    }
                    $name= $img->getClientOriginalName();
                    $request->file('images')[$key]->move('tmpimg/', $name);
                    $image = new Imagick(public_path()."/tmpimg/".$name);
                    $image->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $image->setImageCompressionQuality(40);
                    $image->writeImage(public_path()."/tmpimg/".$name);
                    $imagePath = public_path()."/tmpimg/".$name;
                    $photo = fopen($imagePath, 'r');
                    $uploadFiles = $uploadFiles->attach('files', $photo, $name);
                }
            }
            $uploadFiles = $uploadFiles->post($url.'ba/uploads?ct_id='.$ct_id);
            if(!$uploadFiles->successful()) {
                $uploadFiles->throw();
            } else {
                $names = $uploadFiles["data"];
                foreach($names as $key => $value) {
                    unlink(public_path()."/tmpimg/".$value);
                }
            }
        }
        
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->post($url . 'containerout/add-images', [
            'ct_id' => $request->get('ct_id'),
			'ct_no' => $request->get('upload-image-ct-number'),
            'ct_rusak' => $ct_rusak,
            'images'=>join(";", $names)
		]);        
        if($response->successful()) {

            if($ct_rusak == '1' || $ct_rusak == 1){
                $response = Http::withHeaders([
                    "Accept"=>"application/json",
                    "Authorization"=>"Bearer ".session('token'),
                ])->post($url . 'ba/insert_ba', [
                    'ba_nomor_berita' => 'BA'. '-' .date('dmy'). '-' .$this->randomExternalid(),
                    'ba_tanggal_berita' => date('Y-m-d H:i:s'),
                    'ct_no' => $request->get('upload-image-ct-number'),
                    'ba_petugas' => $admname,
                    'ba_tempat' => '',
                    'ba_keterangan' => '',
                    'ct_rusak' => $ct_rusak,
                    'bm_id' => $bm_id,
                    'status' => 'out'
                ]);

                if($response->successful()) {
                    return redirect()->back()->with("success", "Data Updated successfully");
                }
                else {
                    return $response;
                }
            }

            return redirect()->back()->with("success", "Data Updated successfully");
        }
        if($response->status() == 401) {
            return redirect()->route('login-page');
        }

        if($response->status() == 404) {
            return redirect()->back()->with("error", "gagal update data. ".$response["msg"]);
        }
        return redirect()->back()->with("error", "gagal input data. ".$response["msg"]);
    }

    public function getDocument(Request $request, $ct_id) {
        $url = $url = env('SERVICE');
        $http_type = "GET";
        $service = "GETDOCUMENT";
        $feature = "ADMIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'container/get-document?ct_id='.$ct_id);
        
        if($response->successful()) {
            // return $response;
            $filename = "files/document/$ct_id.pdf";
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            file_put_contents($filename, $response->getBody()->getContents());
            // return file_get_contents($filename, $filename);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename, $response->getBody()->getContents());
        }
    }

    public function getPhoto(Request $request) {

        $url = $url = env('SERVICE');
        $http_type = "GET";
        $service = "GETPHOTO";
        $feature = "ADMIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        $ct_no = $request->ct_no;
        $file_name = $request->ct_gambar;

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get($url . 'container/download-image-by-no-cont', [
            "ct_no" => $ct_no
        ]);
        
        if($response->successful()) {
            // return $response;
            $filename = "files/photo/$ct_no.zip";
            file_put_contents($filename, $response->getBody()->getContents());
            // return $response->getBody()->getContents();
            Isl_log::logResponse ($http_type, 'INFO', $service, $feature, $userid, 'SUCESS', $response, $request);
            return redirect()->back()->with("photo_url", $filename);
        }
    }
}