<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\Response;
use App\Helpers\Repository;
use Auth;

class DashboardController extends Controller
{
    protected $response;
    protected $repository;

    public function __construct(Repository $repository, Response $response)
    {
        $this->repository = $repository;
        $this->response = $response;
    }

    public function index(Request $request)
    {   
        // $isLogin = session('login');
        // if(!$isLogin) {
        //     return redirect()->route('login-page');
        // }

        $allSessions = session()->all();
        $datLogin = $this->repository->GETUSER(Session::get('userid'), Session::get('password'));
        $json = json_decode($datLogin);
        $data = $json->data;
       
        if ($data == [] || $data[0]->status_login = 0) {
            // $flushSessions = session()->flush();
            return redirect()->route('login')
                ->withSuccess('please login first');
        }
        // $request->session()->put($datLogin->json());
        return view('auth.dashboard');
    }
    
}
