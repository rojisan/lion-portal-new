<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use App\Helpers\Mail;
use App\Helpers\Response;
use App\Helpers\Repository;
use App\Models\Counter;
use DataTables;
Use Redirect;

class TiketController extends Controller
{
    public function __construct(Repository $repository, Response $response, Mail $mail)
    {
        $this->repository = $repository;
        $this->response = $response;
        $this->mail = $mail;
    }

    public function tiket(Request $request)
    {
        $usreq = '';
        $categ = '';
        $prior = '';
        $assn = '';
        $stat = '';
        $tick = '';

        $dataUsr = $this->repository->GETUSERBYROLE();
        $json = json_decode($dataUsr, true);

        if($json["rc"] == "00") {
            /* Get User for User Requestor */
            $requestor = $json['requestor'];
            $requestorArray = [];
            foreach ($requestor as $key => $value) {
                array_push($requestorArray, [
                    "NAME" => trim($value['username']),
                    "ID" => trim($value['userid'])
                ]);
            }
            $data['usreq'] = $requestorArray; 
            /* End */

            /* Get Category */
            $category = $json['category'];
            $categoryArray = [];

            foreach ($category as $key => $value) {
                array_push($categoryArray, [
                    "NAME" => trim($value['description']),
                    "ID" => trim($value['categoryid'])
                ]);
            }
            $data['categ'] = $categoryArray; 
            /* End */

            /* Get Priority */
            $priority = $json['priority'];
            $priorityArray = [];

            foreach ($priority as $key => $value) {
                array_push($priorityArray, [
                    "NAME" => trim($value['description']),
                    "ID" => trim($value['priorid'])
                ]);
            }
            $data['prior'] = $priorityArray; 
            /* End */

            /* Get Assigned To */
            $assign = $json['assign'];
            $assignArray = [];

            foreach ($assign as $key => $value) {
                array_push($assignArray, [
                    "NAME" => trim($value['username']),
                    "ID" => trim($value['userid'])
                ]);
            }
            $data['assn'] = $assignArray; 
            /* End */

            /* Get status */
            $status = $json['status'];
            $statusArray = [];
            
            foreach ($status as $key => $value) {
                array_push($statusArray, [
                    "NAME" => trim($value['description']),
                    "ID" => trim($value['statusid'])
                ]);
            }
            $data['stat'] = $statusArray; 
            /* End */

            /* Get Ticket Number */
            $ticketno = $json['ticketno'];
            $ticketnoArray = [];
            
            foreach ($ticketno as $key => $value) {
                array_push($ticketnoArray, [
                    "NAME" => trim($value['ticketno']),
                    "ID" => trim($value['ticketno'])
                ]);
            }
            $data['tick'] = $ticketnoArray; 
            /* End */
        }   

        return view('fitur.tiket', $data);
    }

    public function tiketList(Request $request)
    {
        $userid = Session::get('userid');
        $roleid = Session::get('roleid');

        /* Get Data Ticket */
        $dataTicket = $this->repository->GETTIKET($userid, $roleid);
        $json = json_decode($dataTicket, true);
        
        $dat = '';

        if($json["rc"] == "00") 
        {   
            $dataTrim = $json["data"]['data'];
            $dataTrimArray = [];
            
            foreach ($dataTrim as $key => $value) {
                array_push($dataTrimArray, [
                    "ticketno" => trim($value['ticketno']),
                    "userid" => trim($value['userid']),
                    "requestor" => trim($value['requestor']),
                    "categoryid" => trim($value['categoryid']),
                    "category" => trim($value['category']),
                    "subject" => trim($value['subject']),
                    "attachment" => trim($value['attachment']),
                    "statusid" => trim($value['statusid']),
                    "status" => trim($value['status']),
                    "priorid" => trim($value['priorid']),
                    "priority" => trim($value['priority']),
                    "detail" => $value['detail'],
                    "assignedto" => trim($value['assignedto']),
                    "assigned_to" => trim($value['assigned_to']),
                    "createdon" => trim($value['createdon']),
                    "departmentid" => trim($value['departmentid']),
                    "approvedby_1" => trim($value['approvedby_1']),
                    "approvedby_2" => trim($value['approvedby_2']),
                    "approvedby_3" => trim($value['approvedby_3']),
                    "approvedby_it" => trim($value['approvedby_it']),
                    "rejectedby" => trim($value['rejectedby']),
                    "approvedby1_date" => trim($value['approvedby1_date']),
                    "approvedby2_date" => trim($value['approvedby2_date']),
                    "approvedby3_date" => trim($value['approvedby3_date']),
                    "approvedbyit_date" => trim($value['approvedbyit_date']),
                    "createdby" => trim($value['createdby']),
                ]);
            }
            $data['dat'] = $dataTrimArray;
            
            $statusid = $data['dat'][0]['statusid'];
            $assignedto = $data['dat'][0]['assignedto'];

            /* Session Data */
            $session = array(
                'statusid' => $statusid,
                'assignedto' => $assignedto,
            );
            /* Set User Session */
            Session::put('statusid', $statusid);
            Session::put('assignedto', $assignedto);

        } else {
            $data = []; 
        }   
        $resp = json_encode($data);
        
        return DataTables::of($data['dat'])
            ->addColumn('action', function($row){
                $userid = Session::get('userid');
                $roleid = Session::get('roleid');
                $mgrid = Session::get('mgrid');
                $parentBtn = '<button href="javascript:void(0)" class="view btn btn-success" data-ticket="'.$row["ticketno"].'" data-id="'.$row["userid"].'" data-statusid="'.$row["statusid"].'"
                data-requestor="'.$row["requestor"].'" data-status="'.$row["status"].'" data-category="'.$row["category"].'" data-priority="'.$row["priority"].'" data-subject="'.$row["subject"].'" 
                data-detail="'.$row["detail"].'" data-assignto="'.$row["assigned_to"].'" data-created="'.$row["createdby"].'" data-approve="'.$row["approvedby_1"].'" data-upload="'.$row["attachment"].'">View </button>';
            
                $approveMgrBtn = ' <button href="javascript:void(0)" class="update btn btn-default" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$row["assignedto"].'"
                data-approvedby1="'.$row["approvedby_1"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'" data-approvedby1_date ="'.$row["approvedby1_date"].'">Approve</button>';

                $approveBtn = ' <button href="javascript:void(0)" class="update btn btn-default" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$row["assignedto"].'"
                data-approvedbyit="'.$row["approvedby_it"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'" data-approvedbyit_date ="'.$row["approvedbyit_date"].'">Approve</button>';

                $rejectBtn = ' <button href="javascript:void(0)" class="reject btn btn-danger" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$userid.'"
                data-approvedby1="'.$row["approvedby_it"].'" data-approvedbyit="'.$row["approvedby_it"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'">Reject</button>';
                
                // $superAdminBtn = $parentBtn. $approveBtn. $rejectBtn;
                if($row["categoryid"] == 'CD001' && $row["statusid"] == 'SD006' && $row["assignedto"] == '' ){
                    $itBtn = $parentBtn.' <button href="javascript:void(0)" class="update btn btn-info" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-assignto="'.$row["assignedto"].'"
                    data-approvedby1="'.$row["approvedby_1"].'" data-approvedbyit="'.$mgrid.'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" >Assign To me</button>';
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else  if($row["statusid"] == 'SD002' && $userid == $row["assigned_to"]){
                    $itBtn = $parentBtn.' <button href="javascript:void(0)" class="update btn btn-danger" data-status="'.$row["status"].'" data-statusid="SD003" data-status="'.$row["status"].'" data-assignto="'.$userid.'"
                    data-approvedby1="'.$row["approvedby_1"].'" data-approvedbyit="'.$mgrid.'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$userid.'">Closed</button>';
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else if($row["approvedby_1"] == null && $row["statusid"] == 'SD001' && $userid == $row["assignedto"]){
                    $managerBtn = $parentBtn. $approveMgrBtn. $rejectBtn;
                    $itBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else if($row["approvedby_1"] != null && $row["statusid"] == 'SD001' && $userid == $row["assignedto"] ){
                    $managerItBtn = $parentBtn. $approveBtn. $rejectBtn;
                    $itBtn = $parentBtn;
                    $managerBtn = $parentBtn;
                } else {
                    $itBtn = $parentBtn;
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                }
                
                if($roleid == 'RD004' || $roleid == 'RD005'){
                    return $itBtn;
                }
                if($roleid == 'RD002'){ 
                    return $managerBtn;
                }
                if($roleid == 'RD006'){
                    return $managerItBtn;
                }
                if($roleid == 'RD003'){
                    return $parentBtn;
                }

            })
            ->rawColumns(['action'])
            ->setTotalRecords($json["total"])
            ->setFilteredRecords($json["total"])
            ->make(true);
    }

    public function tiketFilter(Request $request)
    {
        $userid = Session::get('userid');
        $roleid = Session::get('roleid');
        $requestor = $request->requestor;
        $assignto = $request->assignto;
        $status = $request->status;
        $ticketno = $request->ticketno;
        $date_arr = $request->get('daterange');
        $start = explode(' - ',$date_arr)[0];
        $start_date = date("Y-m-d", strtotime($start));
        $end = explode(' - ',$date_arr)[1];
        $end_date = date("Y-m-d", strtotime($end));

        /* Get Filter Ticket */
        $dataFilter = $this->repository->GETFILTERTIKET($userid, $ticketno, $requestor, $assignto, $status, $start_date, $end_date, $roleid);
        // return $dataFilter;
        $json = json_decode($dataFilter, true);
        
        $dat = '';

        if($json["rc"] == "00") 
        {   
            $dataTrim = $json["data"]['data'];
            $dataTrimArray = [];
            
            foreach ($dataTrim as $key => $value) {
                array_push($dataTrimArray, [
                    "ticketno" => trim($value['ticketno']),
                    "userid" => trim($value['userid']),
                    "requestor" => trim($value['requestor']),
                    "categoryid" => trim($value['categoryid']),
                    "category" => trim($value['category']),
                    "subject" => trim($value['subject']),
                    "attachment" => trim($value['attachment']),
                    "statusid" => trim($value['statusid']),
                    "status" => trim($value['status']),
                    "priorid" => trim($value['priorid']),
                    "priority" => trim($value['priority']),
                    "detail" => $value['detail'],
                    "assignedto" => trim($value['assignedto']),
                    "assigned_to" => trim($value['assigned_to']),
                    "createdon" => trim($value['createdon']),
                    "departmentid" => trim($value['departmentid']),
                    "approvedby_1" => trim($value['approvedby_1']),
                    "approvedby_2" => trim($value['approvedby_2']),
                    "approvedby_3" => trim($value['approvedby_3']),
                    "approvedby_it" => trim($value['approvedby_it']),
                    "rejectedby" => trim($value['rejectedby']),
                    "approvedby1_date" => trim($value['approvedby1_date']),
                    "approvedby2_date" => trim($value['approvedby2_date']),
                    "approvedby3_date" => trim($value['approvedby3_date']),
                    "approvedbyit_date" => trim($value['approvedbyit_date']),
                    "createdby" => trim($value['createdby']),
                ]);
            }
            $data['dat'] = $dataTrimArray;

        } else {
            $data = [];
        }   
        $resp = json_encode($data);
    
        return DataTables::of($data['dat'])
            ->addColumn('action', function($row){
                $userid = Session::get('userid');
                $roleid = Session::get('roleid');
                $mgrid = Session::get('mgrid');
                $parentBtn = '<button href="javascript:void(0)" class="view btn btn-success" data-ticket="'.$row["ticketno"].'" data-id="'.$row["userid"].'" data-statusid="'.$row["statusid"].'"
                data-requestor="'.$row["requestor"].'" data-status="'.$row["status"].'" data-category="'.$row["category"].'" data-priority="'.$row["priority"].'" data-subject="'.$row["subject"].'" 
                data-detail="'.$row["detail"].'" data-assignto="'.$row["assigned_to"].'" data-created="'.$row["createdby"].'" data-approve="'.$row["approvedby_1"].'">View </button>';
            
                $approveMgrBtn = ' <button href="javascript:void(0)" class="update btn btn-default" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$row["assignedto"].'"
                data-approvedby1="'.$row["approvedby_1"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'" data-approvedby1_date ="'.$row["approvedby1_date"].'">Approve</button>';

                $approveBtn = ' <button href="javascript:void(0)" class="update btn btn-default" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$row["assignedto"].'"
                data-approvedbyit="'.$row["approvedby_it"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'" data-approvedbyit_date ="'.$row["approvedbyit_date"].'">Approve</button>';

                $rejectBtn = ' <button href="javascript:void(0)" class="reject btn btn-danger" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-status="'.$row["status"].'" data-assignto="'.$userid.'"
                data-approvedby1="'.$row["approvedby_it"].'" data-approvedbyit="'.$row["approvedby_it"].'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$row["userid"].'">Reject</button>';
                
                // $superAdminBtn = $parentBtn. $approveBtn. $rejectBtn;
                if($row["categoryid"] == 'CD001' && $row["statusid"] == 'SD006' && $row["assignedto"] == '' ){
                    $itBtn = $parentBtn.' <button href="javascript:void(0)" class="update btn btn-info" data-status="'.$row["status"].'" data-statusid="'.$row["statusid"].'" data-assignto="'.$row["assignedto"].'"
                    data-approvedby1="'.$row["approvedby_1"].'" data-approvedbyit="'.$mgrid.'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" >Assign To me</button>';
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else  if($row["statusid"] == 'SD002' && $userid == $row["assigned_to"]){
                    $itBtn = $parentBtn.' <button href="javascript:void(0)" class="update btn btn-danger" data-status="'.$row["status"].'" data-statusid="SD003" data-status="'.$row["status"].'" data-assignto="'.$userid.'"
                    data-approvedby1="'.$row["approvedby_1"].'" data-approvedbyit="'.$mgrid.'" data-rejectedby="'.$row["rejectedby"].'" data-ticketno="'.$row["ticketno"].'" data-userid="'.$userid.'">Closed</button>';
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else if($row["approvedby_1"] == null && $row["statusid"] == 'SD001' && $userid == $row["assignedto"]){
                    $managerBtn = $parentBtn. $approveMgrBtn. $rejectBtn;
                    $itBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                } else if($row["approvedby_1"] != null && $row["statusid"] == 'SD001' && $userid == $row["assignedto"] ){
                    $managerItBtn = $parentBtn. $approveBtn. $rejectBtn;
                    $itBtn = $parentBtn;
                    $managerBtn = $parentBtn;
                } else {
                    $itBtn = $parentBtn;
                    $managerBtn = $parentBtn;
                    $managerItBtn = $parentBtn;
                }
                
                if($roleid == 'RD004' || $roleid == 'RD005'){
                    return $itBtn;
                }
                if($roleid == 'RD002'){ 
                    return $managerBtn;
                }
                if($roleid == 'RD006'){
                    return $managerItBtn;
                }
                if($roleid == 'RD003'){
                    return $parentBtn;
                }
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->setTotalRecords($json["total"])
            ->setFilteredRecords($json["total"])
            ->make(true);
    }

    public function addTiket(Request $request)
    {

        $userid = Session::get('userid');
        $roleid = Session::get('roleid');
        $spvid = Session::get('spvid');
        $mgrid = Session::get('mgrid');
        $departmentid = Session::get('departmentid');
        $createdby = Session::get('userid');

        $createdon = date('Y-m-d');
        $userreq = $request->user;
        $category = $request->category;
        $priority = $request->priority;
        $subject = $request->subject;
        $remark = $request->detail;
       
        /* Get File Upload */
        if (!empty($request->file('files'))){
            $file = $request->file('files');
            $file_name = 'Ticket_File_'.date('Y-m-d').'.'.$file->extension();  
            $file->move(public_path('uploads'), $file_name);
            $files[]= $file_name;
        } else {
            $files = [''];
        }
        // foreach ($files as $key => $file) {
        //     File::create($file);
        // }
        /* End */

        /* Generate Ticket Number */ 
        $year = date("Y");
        $dataPrefix = DB::connection('pgsql')->table('master_data.m_counter')->where('counterid', 'CT001')->where('period', $year)->get();
        $prefix = $dataPrefix[0]->prefix;
        $period = $dataPrefix[0]->period;
        $start_numb = $dataPrefix[0]->start_number;
        $end_numb = $dataPrefix[0]->end_number;
        $last = $dataPrefix[0]->last_number;
        /* Session Data */
        $session = array(
            'last_number' => $last,
            'uploads' => $files,
        );
        /* Set User Session */
        Session::put('last_number', $last);
        Session::put('uploads', $files);
        $lastSession = Session::get('last_number');
        if ($start_numb <= $end_numb && $last == $lastSession){
            $last_numb =  str_pad($dataPrefix[0]->last_number + 1, 4, "00", STR_PAD_LEFT);

        } else 
            $last_numb = '0000';
        
        $ticketno = $prefix. $period. $last_numb;
        /* End */

        /* Validasi Approve manager by user login */
        $dataApprove = $this->repository->GETAPPROVEBYDEPARTMENT($departmentid, $userid);
        $mgridApprove = $dataApprove['data'][0]['mgrid'];
        $userApprove = $dataApprove['data'][0]['userid'];

        $dataMgrIt = DB::connection('pgsql')->table('master_data.m_user')->where('roleid', 'RD006')->get();
        $mgrIt = $dataMgrIt[0]->mgrid;
        if($mgrid == '' && $roleid == 'RD002' && $userid == $userApprove){
            $assign = $mgrIt;
            $approvedby_1 = $userid;
            $approvedby_it = '';
            $auth = true;
        } else if ($mgrid == '' && $roleid == 'RD006' && $userid == $userApprove){
            $assign = $request->assign;
            $approvedby_1 = $userid;
            $approvedby_it = $userid;
            $auth = true;
        } else if($roleid == 'RD004' || $roleid == 'RD005') {
            $assign = $userid;
            $approvedby_1 = '';
            $approvedby_it = $mgrid;
            $auth = true;
        } else {
            $assign = $mgrid;
            $approvedby_1 = '';
            $approvedby_it = '';
            $auth = true;
        }
        /* End */

        /* Validasi Category Incident */
        $dataCategory = DB::connection('pgsql')->table('master_data.m_category')->where('categoryid', $category)->get();
        $flaggingCat =  $dataCategory[0]->approval;
        if ($flaggingCat == 'X' ){
            if ($roleid == 'RD002'){
                $status = 'OPEN';
                $statusid = 'SD006';
            } else if ($roleid == 'RD003'){
                $status = 'WAITING FOR APPROVAL';
                $statusid = 'SD001';
                $auth = true;
            } else if($roleid == 'RD004' || $roleid == 'RD005') {
                $status = 'IN PROGRESS';
                $statusid = 'SD002';
                $auth = true;
            } else {
                $status = 'IN PROGRESS';
                $statusid = 'SD002';
                $auth = true;
            }
        } else {
            $assign = '';
            $approvedby_1 = '';
            $approvedby_it = '';
            $status = 'OPEN';
            $statusid = 'SD006';
            $auth = true;
        }
        /* End */
        $upload = Session::get('uploads');
        if ($auth){
            $addTicket = $this->repository->ADDTIKET($ticketno, $userreq, $category, $userid, $subject, $assign, $statusid, $createdon, $approvedby_1, $approvedby_it, $priority, $remark, $createdby, $departmentid, $upload, $roleid);

            $updateCounter = $this->repository->UPDATECOUNTER($last);

            $SendMail = $this->mail->SENDMAIL($ticketno, $category, $priority, $subject, $remark, $status, $assign); 
        }
    
        return redirect()->route('tiket')->with("success", "successfully");

    }

    public function updateTiket(Request $request)
    {   

        $userid = Session::get('userid');
        $roleid = Session::get('roleid');
        $mgrid = Session::get('mgrid');
        $ticketno = $request->ticketno;
        $assignto = $request->assignto;
        $assign = $request->assignto;
        $approvedby1 = $request->approvedby1;
        $approveby_it = $request->approveby_it;
        $rejectedby = $request->rejectedby;
        $statusid = $request->statusid;
        $status = $request->status;
        $approveby_1_date = $request->approvedby1_date;
        $approveby_it_date = $request->approvedbyit_date;
        
        /* Get Data Ticket */
        $dataTicketapprove = $this->repository->GETTICKETAPPROVE($userid, $ticketno, $roleid);
        $json = json_decode($dataTicketapprove, true);
        $category = $json['data'][0]['category'];
        $priority = $json['data'][0]['priority'];
        $subject = $json['data'][0]['subject'];
        $status = $json['data'][0]['status'];
        $remark = $json['data'][0]['detail'];

        /* Get User Email */ 
        if(empty($mgrid) || $mgrid == null){
            $dataEmail = DB::connection('pgsql')->table('master_data.m_user')->where('userid', $assignto)->where('mgrid', $userid)->get();
            $email = $dataEmail[0]->usermail;
        } else {
            $dataEmail = DB::connection('pgsql')->table('master_data.m_user')->where('mgrid', $mgrid)->where('userid', $assignto)->get();
            $email = $dataEmail[0]->usermail;
        }
        

        $updateTicket = $this->repository->UPDATETICKET($userid, $ticketno, $assignto, $approvedby1, $approveby_it, $rejectedby, $statusid, $approveby_1_date, $approveby_it_date, $roleid);

        $SendMail = $this->mail->SENDMAIL($ticketno, $category, $priority, $subject, $remark, $status, $assign, $email); 

        return redirect()->route('tiket')->with("success", "successfully");;
    }

    public function downloadFile(Request $request)
    {    
        $dataFile = Session::get('uploads');
        if ($dataFile != ['']){
            $dataFile = Session::get('uploads');
            $filepath = public_path()."/uploads/".$dataFile[0];
            
            $headers = array(
                'Content-Type: application/pdf',
            );

            return response()->download($filepath, $dataFile[0], $headers);
        } else {
            return back()->withErrors([
                'File' => 'File Not Found',
            ]);
        } 
    }
}
