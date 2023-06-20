<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Useraccount;
use App\Models\User;
use App\Helpers\Response;
use App\Helpers\Repository;



class LoginController extends Controller
{
    protected $response;
    protected $repository;

    public function __construct(Repository $repository, Response $response)
    {
        $this->repository = $repository;
        $this->response = $response;
    }
    
    public function showLoginPage()
    {
        return view('auth.login');
    }

    public function updateLogin ($userid, $password){
        $date = date('Y-m-d H:i:s');
       
        try {
            DB::connection('pgsql')->table('master_data.m_user')->where('userid', $userid)->update([
                'status_login' => 1,
                'createdon' => $date
            ]);
            DB::commit();
            return $this->response->SUCCESS('');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function updateLogout ($userid, $password){
        $date = date('Y-m-d H:i:s');

        try {
            DB::connection('pgsql')->table('master_data.m_user')->where('userid', $userid)->update([
                'status_login' => 0,
                'createdon' => $date
            ]);
            DB::commit();
            return $this->response->SUCCESS('');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function login(Request $request){
        $userid = $request->userid;
        $password = $request->password;

        $credentials = $request->validate([
            "userid"=>"required",
            "password"=>"required"
        ]);

        $datLogin = $this->repository->GETUSER($userid, $password);
        $json = json_decode($datLogin);
        $data = $json->data;

        /* Update status */
        if(empty($data)){
            $update = $this->updateLogin($userid, $password);
        } else {
            if ($data[0]->status_login == 0)
            {
                
            } else {
                return back()->withErrors([
                    'userid' => 'If you are already logged in, please log out first or call admin',
                ]);
            }
        }
      
        if($json->rc == '00')
        {
            /* checked status login */
            if ($data[0]->status_login == 1){
                return back()->withErrors([
                    'userid' => 'If you are already logged in, please log out first ',
                ]);
            }

            $userid = $data[0]->userid;
            $username = $data[0]->username;
            $password = $data[0]->pass;
            $departmentid = $data[0]->departmentid;
            $usermail = $data[0]->usermail;
            $status = $data[0]->status_login;
            $roleid = $data[0]->roleid;
            $plantid = $data[0]->plantid;
            $spvid = $data[0]->spvid;
            $mgrid = $data[0]->mgrid;

            /* Session Data */
            $session = array(
                'userid' => $userid,
                'username' => $username,
                'password' => $password,
                'departmentid' => $departmentid,
                'usermail' => $usermail,
                'roleid' => $roleid,
                'plantud' => $plantid,
                'spvid' => $spvid,
                'mgrid' => $mgrid,
                'status' => $status
            );
            /* Set User Session */
            Session::put('login', true);
            Session::put('userid', $userid);
            Session::put('username', $username);
            Session::put('password', $password);
            Session::put('departmentid', $departmentid);
            Session::put('usermail', $usermail);
            Session::put('roleid', $roleid);
            Session::put('plantid', $plantid);
            Session::put('spvid', $spvid);
            Session::put('mgrid', $mgrid);
            Session::put('status', $status);
       
            return redirect()->route('home')
                ->withSuccess('You have successfully logged in!');
        } else {

            return back()->withErrors(['error' => 'your password or id is wrong ',]);
        }
    } 

    public function logout(Request $request)
    {
        $userid = Session::get('userid');
        $password = Session::get('password');
     
        $datLogin = $this->repository->GETUSER($userid, $password);
        $json = json_decode($datLogin);
        $data = $json->data;

        /* Update Status Login */
        $update = $this->updateLogout($userid, $password); 
        /* End */ 

        return redirect()->route('login');
    }  
    
}
