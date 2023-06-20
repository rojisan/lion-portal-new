<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use App\Helpers\Isl_log;
use Imagick;

class KraniController extends Controller
{
    function uploadImages(Request $request) {
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
            }
        }
        
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($url . 'container/update-image', [
            'ct_id' => $request->get('ct_id'),
			'ct_no' => $request->get('upload-image-ct-number'),
            'ct_rusak' => $ct_rusak,
            'ct_gambar'=>join(";", $names)
		]);
    
        if($response->successful()) {

            if($ct_rusak == 'ya'){
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
                    'status' => 'in'
                ]);

                if($response->successful()) {
                
                    return redirect()->route('index')->with("success", "Data Updated successfully");
                }
            }

            return redirect()->route('index')->with("success", "Data Updated successfully");
           
        }

        if($response->status() == 401) {
            return redirect()->route('login-page');
        }

        if($response->status() == 404) {
            return redirect()->back()->with("error", "gagal update data. ".$response["msg"]);
        }

        return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
    }

    public function randomExternalid() {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 3; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
