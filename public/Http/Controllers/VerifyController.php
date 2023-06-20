<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use App\Helpers\Isl_log;

class VerifyController extends Controller
{
    function uploadDocs(Request $request) {
        $url = env('SERVICE');
        $data = $request->hasFile('images');
        $datenow = new \DateTime();

        /*Log Request*/
        $http_type = "GET";
        $service = "UPLOADDOCS";
        $feature = "VERIFY";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/
  
        $path_list = array();
        if($data) {
            $i = 1;
            foreach($request->file('images') as $doc) {
                // $filename = $datenow->getTimestamp();
                $filename = date('dmY'). '-' .$this->randomExternalid();
                $path = Storage::putFileAs("public/ct_docs/".session('username')."/".$filename, new File($doc), $filename.".".$doc->getClientOriginalExtension());
                $path = explode("/", $path);
                $path[0] = "storage";
                array_push($path_list, join("/",$path));
                $i++;
            }
        }
        
        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->patch($url . 'container/update-document', [
            'ct_id' => $request->get('ct_id'),
            'ct_document'=>join(";", $path_list)
		]);
        
        if($response->successful()) {

            return redirect()->route('index')->with("success", "Data Updated successfully");
           
        }

        if($response->status() == 401) {
            return redirect()->route('login-page');
        }

        if($response->status() == 404) {
            return redirect()->back()->with("error", "gagal input data. ".$response["detail"][0]["msg"]);
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
