<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Auth;
use App\Helpers\Isl_log;

class LoginController extends Controller
{

    public function showLoginPage(Request $request) {
        $url = env('SERVICE');
        $isLogin = $request->session()->get('login', function() {
            return false;
        });

        /*Log Request*/
        $http_type = "GET";
        $service = "SHOWLOGINPAGE";
        $feature = "LOGIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        if($isLogin) {
            return redirect('/');
        } else {
            $username = Session::get('username');

            $response = Http::withHeaders([
                "Accept"=>"application/json",
                "Authorization"=>"Bearer ".session('token'),
            ])->asForm()->get($url . 'reset_user?name='. $username);
        
            return view('auth.login');
        }
        
    }

    public function login(Request $request)
    {
        $url = env('SERVICE');

        $request->validate([
            "username"=>"required",
            "password"=>"required"
        ]);

        $username = $request->username;
        $password = $request->password;

        /*Log Request*/
        $http_type = "POST";
        $service = "LOGIN";
        $feature = "LOGIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
        ])->asForm()->post($url . 'token', [
			'username' => $username,
			'password' => $password
		]);
        
        if($response->successful()){
            $token = $response['access_token'];
            $token_type = $response['token_type'];

            /* Session data */
            $session = array(
                'token' => $token,
                'tokentype' => $token_type,
                'username' => $username,
                'login' => true,
            );

            /* Set User Session */
            $request->session()->put($session);
            return redirect()->route('home');

        } else if($response->failed()){
            $msg = $response['message_data'];

            return Redirect::back()->withErrors([$msg]);

        } else {
            return redirect()->route('login')->with('error','Failed Login');
        }
        
    }

    public function logout(Request $request) 
    {   
        $url = env('SERVICE');

        $username = Session::get('username');
        $request->session()->flush();

        /*Log Request*/
        $http_type = "GET";
        $service = "LOGOUT";
        $feature = "LOGIN";
        $userid = Session::get('user_type');
        $request_body = "";
        $request_header = '{"Content-Type":["application/json" ]}';
        Isl_log::logRequest ($http_type, 'INFO', $service, $feature, $userid, $request_body, $request_header);
        /*End Log Request*/

        $response = Http::withHeaders([
            "Accept"=>"application/json",
        ])->asForm()->get($url . 'reset_user?name='. $username);
      
        return redirect('login');        
    }
}
