<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Auth;
use App\Helpers\Isl_log;

class DashboardController extends Controller
{
    protected $getuserinfo;
    public function __construct() {
        $this->getuserinfo = env('SERVICE')."admin/whoami";
    }

    public function index(Request $request) 
    {   
        // $isLogin = session('login');
        // if(!$isLogin) {
        //     return redirect()->route('login-page');
        // }

        /*Log Request*/
        $http_type = "GET";
        $service = "INDEX";
        $feature = "DASHBOARD";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
            "Authorization"=>"Bearer ".session('token'),
        ])->asForm()->get( $this->getuserinfo);
        if($response->status() == 401) {
            $request->session()->flush();
            return redirect()->route('login-page');
        }
        $request->session()->put($response->json());
        return view('auth.dashboard');
    }
}
