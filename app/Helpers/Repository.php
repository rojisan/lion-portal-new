<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Useraccount;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Tiket;
use App\Models\Tiketdiscussion;
use App\Models\Tiketpriority;
use App\Models\Tiketstatus;
use Auth;

class Repository
{
    public static function GETUSER($userid, $password)
    {
        if(DB::connection('pgsql')->table('master_data.m_user')->where('userid', $userid)->where('pass', $password)->exists())
        {
            $data = DB::connection('pgsql')->table('master_data.m_user as a')
                    ->join('master_data.m_department as b', 'a.departmentid', '=', 'b.departmentid')
                    ->where ('userid', $userid)
                    ->where ('pass', $password)
                    ->get();
            $count = DB::connection('pgsql')->table('master_data.m_user')->where('userid', $userid)->count();

            DB::commit();
            $response = array(
                'rc' => '00',
                'msg' => 'success',
                'data' => $data,
                'count' => $count
            );
        } else {
            $response = array(
                'rc' => '01',
                'msg' => 'Wrong User Or Password ',
                'data' => [],
                'count' => []
            );
        }
        return json_encode($response);
    }

    public static function GETABSEN($id, $userid, $start_date, $end_date, $roleid, $departmentid, $mgrid)
    {   
        // $start_date = "2023-06-01";
        // $end_date = "2023-06-19";
        $datenow = date('Y-m-d');

        $datauser = DB::connection('pgsql')->table('master_data.m_user')
                    ->where ('mgrid', $userid)
                    ->get();

        $response = array(
            'data' => $datauser,
        );
        $dat_arr = $response['data']; 
        
        if ($roleid == 'RD006' || $roleid == 'RD002'){
            $arr_user = array();
            foreach($dat_arr as $key => $value){
                array_push($arr_user, [
                    substr($value->userid, 2),
                ]);
            };
        } else {
            $arr_user = array(
                $id
            );
        }
        
        if(DB::connection('mysql')->table('dbstaff.kartuabsensi')->where('id', $id)->exists()){

            // $count1 = DB::table('dbstaff.kartuabsensi')->where('id', $id)->where('tgl', '>', now()->subDays(30)->endOfDay())->count();
            // $count = Attendance::select("SELECT count(*) FROM dbstaff.kartuabsensi WHERE id = '$id'");
            // $countfilter = DB::table('dbstaff.kartuabsensi')->where('id', $id)->whereBetween(DB::raw('DATE(tgl)'), [$start_date, $end_date])->count();

            $count = DB::table('dbstaff.kartuabsensi')
                ->where('id', $id)
                ->where('tgl', '>', now()->subDays(30)->endOfDay())
                ->groupBy('id')
                ->count();
            
            $countfilter = DB::table('dbstaff.kartuabsensi')
                ->whereBetween(DB::raw('DATE(tgl)'), [$start_date, $end_date])
                ->groupBy('id')
                ->count();
          
            
            if ($start_date == $datenow && $end_date == $datenow){
                // $data = Attendance::where('tgl', '>', now()->subDays(30)->endOfDay())
                //     ->whereIn('ID', $arr_user)
                //     ->groupBy('id')
                //     ->limit(10)
                //     ->simplePaginate($count);

                $data = DB::table('dbstaff.kartuabsensi')
                    ->where('tgl', '>', now()->subDays(30)->endOfDay())
                    ->whereIn('id', $arr_user)
                    ->groupBy('notr')
                    ->limit(10)
                    ->simplePaginate($count);
              
                if($data->isNotEmpty()){
                    $response = array(
                        'rc' => '00',
                        'msg' => 'success',
                        'data' => $data,
                        'total' => $count
                    );
                } else {
                    $response = array(
                        'rc' => '01',
                        'msg' => 'failed',
                        'data' => [],
                        'total' => []
                    );
                }
            } else {
                $data = DB::table('dbstaff.kartuabsensi')
                ->whereBetween(DB::raw('DATE(tgl)'), [$start_date, $end_date])
                ->whereIn('id', $arr_user)
                ->groupBy('notr')
                ->limit(10)
                ->simplePaginate($countfilter);

                if($data->isNotEmpty()){
                    $response = array(
                        'rc' => '00',
                        'msg' => 'success',
                        'data' => $data,
                        'total' => $count
                    );
                } else {
                    $response = array(
                        'rc' => '01',
                        'msg' => 'failed',
                        'data' => [],
                        'total' => []
                    );
                }
            }
            return json_encode($response);
        } else {
            $response = array(
                'rc' => '01',
                'msg' => 'failed',
                'data' => [],
                'total' => []
            );
        }
        return json_encode($response);
    }

    public static function GETTIKET($userid, $roleid)
    {   
        try{
            if ($roleid == 'RD004' || $roleid == 'RD005' || $roleid == 'RD006') {

                $count = DB::connection('pgsql')->table('helpdesk.t_ticket')->count();
                $data =  DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 
                    'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                    ->limit(10)
                    ->simplePaginate($count);

            } else {
                $count = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 'a.createdon', 'b.departmentid', 'a.detail', 'b.mgrid')
                    ->where('a.userid', $userid)
                    ->orWhere('b.mgrid', $userid)
                    ->orWhere('a.assignedto', $userid)
                    ->orWhere('a.assignedto', '')
                    ->count();
    
                $data = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 
                    'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                    ->where('a.userid', $userid)
                    ->orWhere('b.mgrid', $userid)
                    ->orWhere('a.assignedto', '')
                    ->orderBy('a.ticketno', 'DESC')
                    ->limit(10)
                    ->simplePaginate($count);

            }
           
            $response = array(
                'rc' => '00',
                'msg' => 'success',
                'data' => $data,
                'total' => $count
            );
            
        } catch(\Exception $e) {
            return $e->getMessage();
        }  
        return json_encode($response);   
    }

    public static function GETFILTERTIKET($userid, $ticketno, $requestor, $assignto, $status, $start_date, $end_date, $roleid)
    {   
        // $start_date = "2023-06-01";
        // $end_date = "2023-06-19";
        // $status = "SD00";
        // $ticketno = "HLP20230002";
        // $assignto = "101017";
        try{
            if ($roleid == 'RD004' || $roleid == 'RD005' || $roleid == 'RD006') {
                // $count = DB::connection('pgsql')->table('helpdesk.t_ticket')->count();
                $count = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 
                    'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                    ->where('a.ticketno', 'LIKE','%'.$ticketno.'%') 
                    ->Where('a.statusid', 'LIKE','%'.$status.'%') 
                    ->where('a.userid', 'LIKE','%'.$requestor.'%') 
                    ->where('a.assignedto', 'LIKE','%'.$assignto.'%')
                    ->whereBetween(DB::raw('DATE(a.createdon)'), [$start_date, $end_date]) 
                    ->count();
               
                $data =  DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 
                    'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                    ->where('a.ticketno', 'LIKE','%'.$ticketno.'%') 
                    ->Where('a.statusid', 'LIKE','%'.$status.'%') 
                    ->where('a.userid', 'LIKE','%'.$requestor.'%') 
                    ->where('a.assignedto', 'LIKE','%'.$assignto.'%') 
                    ->whereBetween(DB::raw('DATE(a.createdon)'), [$start_date, $end_date])
                    ->limit(10)
                    ->simplePaginate($count);
                // return $data;
            } else {
                $count = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','b.username as assigned_to', 'a.createdon', 'b.departmentid', 'a.detail', 'b.mgrid')
                    ->where('a.userid', $userid)
                    ->orWhere('b.mgrid', $userid)
                    ->orWhere('a.assignedto', $userid)
                    ->orWhere('a.assignedto', '')
                    ->orWhere('a.statusid', 'LIKE','%'.$status.'%') 
                    ->orWhere('a.ticketno', 'LIKE','%'.$ticketno.'%') 
                    ->orWhere('a.userid', 'LIKE','%'.$requestor.'%') 
                    ->orWhere('a.assignedto', 'LIKE','%'.$assignto.'%') 
                    ->whereBetween(DB::raw('DATE(a.createdon)'), [$start_date, $end_date])
                    ->count();
    
                $data = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                    ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                    ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                    ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                    ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                    ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','b.username as assigned_to', 
                    'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                    ->where('a.userid', $userid)
                    ->orWhere('b.mgrid', $userid)
                    ->orWhere('a.assignedto', '')
                    ->orWhere('a.statusid', 'LIKE','%'.$status.'%') 
                    ->orWhere('a.ticketno', 'LIKE','%'.$ticketno.'%') 
                    ->orWhere('a.userid', 'LIKE','%'.$requestor.'%') 
                    ->orWhere('a.assignedto', 'LIKE','%'.$assignto.'%') 
                    ->whereBetween(DB::raw('DATE(a.createdon)'), [$start_date, $end_date])
                    ->orderBy('a.ticketno', 'DESC')
                    ->limit(10)
                    ->simplePaginate($count);

            }
          
            $response = array(
                'rc' => '00',
                'msg' => 'success',
                'data' => $data,
                'total' => $count
            );
            
        } catch(\Exception $e) {
            return $e->getMessage();
        }  
        return json_encode($response);   
    }

    public static function GETTICKETAPPROVE($userid, $ticketno, $roleid)
    {
        try{
            $data = DB::connection('pgsql')->table('helpdesk.t_ticket as a')
                        ->join('master_data.m_user as b', 'a.userid', '=', 'b.userid')
                        ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'a.assignedto', 'a.createdon', 'b.mgrid' )
                        ->join('master_data.m_ticket_priority as c', 'a.priorid', '=', 'c.priorid')
                        ->select('a.ticketno', 'a.userid', 'b.username', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'a.priorid', 'c.description', 'a.assignedto', 'a.createdon' )
                        ->join('master_data.m_ticket_status as d', 'a.statusid', '=', 'd.statusid')
                        ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid', 'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                        ->join('master_data.m_category as e', 'a.categoryid', '=', 'e.categoryid')
                        ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto', 'a.createdon' )
                        ->leftjoin('master_data.m_user as f', 'a.assignedto', '=', 'f.userid')
                        ->select('a.ticketno', 'a.userid', 'b.username as requestor', 'a.categoryid','e.description as category',  'a.subject', 'a.attachment', 'a.statusid', 'd.description as status','a.priorid', 'c.description as priority', 'a.assignedto','f.username as assigned_to', 
                        'a.createdon', 'b.departmentid', 'a.detail', 'a.approvedby_1', 'a.approvedby_2', 'a.approvedby_3', 'a.approvedby_it', 'a.rejectedby', 'a.createdby', 'b.mgrid', 'a.approvedby1_date', 'a.approvedby2_date', 'a.approvedby3_date', 'a.approvedbyit_date')
                        ->where('a.ticketno', $ticketno)
                        ->orderBy('a.ticketno', 'DESC')
                        ->get();
            
                $response = array(
                    'rc' => '00',
                    'msg' => 'success',
                    'data' => $data
                );
            
        } catch(\Exception $e) {
            return $e->getMessage();
        }  
        return json_encode($response);   
    
    }
    
    public static function GETUSERBYROLE()
    {   
        try{
            
            $requestor = DB::connection('pgsql')->table('master_data.m_user as a')
            ->join('master_data.m_role as b', 'a.roleid', '=', 'b.roleid')
            ->whereIn('b.roleid', ['RD002','RD003','RD006'])
            ->get();

            $category = DB::connection('pgsql')->table('master_data.m_category')
            ->get();

            $priority = DB::connection('pgsql')->table('master_data.m_ticket_priority')
            ->get();

            $status = DB::connection('pgsql')->table('master_data.m_ticket_status')
            ->get();

            $ticketno = DB::connection('pgsql')->table('helpdesk.t_ticket')
            ->get();

            $assign = DB::connection('pgsql')->table('master_data.m_user as a')
            ->join('master_data.m_department as b', 'a.departmentid', '=', 'b.departmentid')
            ->whereIn('b.departmentid', ['DD001'])
            ->get();

            $response = array(
                'rc' => '00',
                'msg' => 'success',
                'requestor' => $requestor,
                'category' => $category,
                'priority' => $priority,
                'assign' => $assign,
                'status' => $status,
                'ticketno' => $ticketno,
            );
            
        } catch(\Exception $e) {
            return $e->getMessage();
        }  
        return json_encode($response);  
         
    }

    public static function GETAPPROVEBYDEPARTMENT($departmentid, $userid)
    {
        try{
            $datauser = DB::connection('pgsql')->table('master_data.m_user')
                ->where('departmentid', $departmentid)
                ->orWhere('userid', $userid)
                ->get();

            $response = array(
                'data' => $datauser,
            );
            $dataTrimArray = $response['data']; 

            $arr_user = array();
            foreach ($dataTrimArray as $key => $value) {
                array_push($arr_user, [
                    "userid" => trim($value->userid),
                    "username" => trim($value->username),
                    "pass" => trim($value->pass),
                    "departmentid" => trim($value->departmentid),
                    "plantid" => trim($value->plantid),
                    "roleid" => trim($value->roleid),
                    "spvid" => trim($value->spvid),
                    "mgrid" => trim($value->mgrid),
                    "usermail" => trim($value->usermail),
                    "createdon" => trim($value->createdon),
                ]);
            }
            $response = array(
                'rc' => '00',
                'msg' => 'success',
                'data' => $arr_user
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }  
        return $response;
    }
    public static function ADDTIKET($ticketno, $userreq, $category, $userid, $subject, $assign, $statusid, $createdon, $approvedby_1, $approvedby_it, $priority, $remark, $createdby, $departmentid, $upload, $roleid)
    {      
        /* passing Data to Array */
        if ($roleid == 'RD002' || $roleid == 'RD003') {
            $value = array(
                'ticketno' => $ticketno,
                'categoryid' => $category,
                'userid' => $userid,
                'subject' => $subject,
                'detail' => $remark,
                'attachment' => $upload,
                'assignedto' => $assign,
                'statusid' => $statusid,
                'createdon' => $createdon,
                'approvedby_1' => $approvedby_1,
                'approvedby_2' => '',
                'approvedby_3' => '',
                'approvedby_it' => $approvedby_it,
                'priorid' => $priority,
                'rejectedby' => '',
                'remark' => $remark,
                'approvedby1_date' => date('Y-m-d'),
                'approvedby2_date' => date('Y-m-d'),
                'approvedby3_date' => date('Y-m-d'),
                'approvedbyit_date' => date('Y-m-d'),
                'createdby' => $createdby
            );
    } else {
            $value = array(
                'ticketno' => $ticketno,
                'categoryid' => $category,
                'userid' => $userreq,
                'subject' => $subject,
                'detail' => $remark,
                'attachment' => $upload,
                'assignedto' => $assign,
                'statusid' => $statusid,
                'createdon' => $createdon,
                'approvedby_1' => $approvedby_1,
                'approvedby_2' => '',
                'approvedby_3' => '',
                'approvedby_it' => $approvedby_it,
                'priorid' => $priority,
                'rejectedby' => '',
                'remark' => $remark,
                'approvedby1_date' => date('Y-m-d'),
                'approvedby2_date' => date('Y-m-d'),
                'approvedby3_date' => date('Y-m-d'),
                'approvedbyit_date' => date('Y-m-d'),
                'createdby' => $createdby
            );
        }
        $insert = DB::connection('pgsql')->table('helpdesk.t_ticket')->insert([
                    'ticketno' => $value['ticketno'],
                    'categoryid' => $value['categoryid'],
                    'userid' => $value['userid'],
                    'subject' => $value['subject'],
                    'detail' => $value['detail'],
                    'attachment' => $value['attachment'][0],
                    'assignedto' => $value['assignedto'],
                    'statusid' => $value['statusid'],
                    'createdon' => $value['createdon'],
                    'approvedby_1' => $value['approvedby_1'],
                    'approvedby_2' => '',
                    'approvedby_3' => '',
                    'approvedby_it' => $value['approvedby_it'],
                    'priorid' => $value['priorid'],
                    'rejectedby' => '',
                    'remark' => $value['rejectedby'],
                    'approvedby1_date' => $value['approvedby1_date'],
                    'approvedby2_date' => $value['approvedby2_date'],
                    'approvedby3_date' => $value['approvedby3_date'],
                    'approvedbyit_date' => $value['approvedbyit_date'],
                    'createdby' => $value['createdby']
                ]);

        DB::commit();
        if(!empty($insert)){
            return response()->json([
                'rc' => '00',
                'desc' => 'success',
                'msg' => 'success',
                'data' => $insert
            ]);
        } else {
            return response()->json([
                'rc' => '01',
                'desc' => 'failed',
                'msg' => 'failed',
                'data' => $insert
            ]);
        }
    }   

    public static function UPDATECOUNTER($last)
    {   
        DB::beginTransaction();
        $update = DB::connection('pgsql')->table('master_data.m_counter')->update([
            'last_number' => $last + 1
        ]);
        DB::commit();
        if(!empty($update)){
            return response()->json([
                'rc' => '00',
                'desc' => 'success',
                'msg' => 'success',
                'data' => $update
            ]);
        } else {
            return response()->json([
                'rc' => '01',
                'desc' => 'failed',
                'msg' => 'failed',
                'data' => $update
            ]);
        }

    }

    public static function UPDATETICKET($userid, $ticketno, $assignto, $approvedby1, $approveby_it, $rejectedby, $statusid, $approveby_1_date, $approveby_it_date, $roleid)
    {   
        DB::beginTransaction();
        if($roleid == "RD006"){
            $update = DB::connection('pgsql')->table('helpdesk.t_ticket')
            ->Where('ticketno', $ticketno)
            ->update([
                'assignedto' => $assignto,
                'statusid' => $statusid,
                'approvedby_it' => $approveby_it,
                'rejectedby' => $rejectedby,
                'approvedbyit_date' => $approveby_it_date,
            ]);
        } else if($roleid == "RD002"){
            $update = DB::connection('pgsql')->table('helpdesk.t_ticket')
            ->Where('ticketno', $ticketno)
            ->update([
                'assignedto' => $assignto,
                'statusid' => $statusid,
                'approvedby_1' => $approvedby1,
                'rejectedby' => $rejectedby,
                'approvedby1_date' => $approveby_1_date,
            ]);
        } else {
            $update = DB::connection('pgsql')->table('helpdesk.t_ticket')
            ->Where('ticketno', $ticketno)
            ->update([
                'assignedto' => $assignto,
                'statusid' => $statusid,
                'approvedby_1' => $approvedby1,
                'approvedby_it' => $approveby_it,
            ]);
        }
       
        DB::commit();
        if(!empty($update)){
            return response()->json([
                'rc' => '00',
                'desc' => 'success',
                'msg' => 'success',
                'data' => $update
            ]);
        } else {
            return response()->json([
                'rc' => '01',
                'desc' => 'failed',
                'msg' => 'failed',
                'data' => $update
            ]);
        }

    }
}
