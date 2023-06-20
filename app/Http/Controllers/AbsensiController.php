<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Response;
use App\Helpers\Repository;
use DataTables;

class AbsensiController extends Controller
{
    public function __construct(Repository $repository, Response $response)
    {
        $this->repository = $repository;
        $this->response = $response;
    }

    public function absensi(Request $request)
    {
        return view('fitur.absensi');
    }
    
    public function getAbsensi(Request $request)
    {

        $userid = Session::get('userid');
        $roleid = Session::get('roleid');
        $departementid = Session::get('departementid');
        $mgrid = Session::get('mgrid');
        $id =  substr($userid, 2);
        $date_arr = $request->get('daterange');
        $start = explode(' - ',$date_arr)[0];
        $start_date = date("Y-m-d", strtotime($start));
        $end = explode(' - ',$date_arr)[1];
        $end_date = date("Y-m-d", strtotime($end));
        
        /* GET DATA ABSEN */
        $dataAbsen = $this->repository->GETABSEN($id, $userid, $start_date, $end_date, $roleid, $departementid, $mgrid);
        $json = json_decode($dataAbsen, true);
        
        if($json["rc"] == "00") 
        {   
            $data = $json['data']['data'];
        } else {
            $data = [];
        }
        
        return DataTables::of($data)
            ->setTotalRecords($json["total"])
            ->setFilteredRecords($json["total"])
            // ->skipPaging()
            ->make(true);

        // return view('fitur.absensi', $data);
    }

}
