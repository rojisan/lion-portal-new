<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail as SendtoMail;
use App\Mail\SendMail;

use Auth;

class Mail
{

    public static function SENDMAIL($ticketno, $category, $priority, $subject, $remark, $status, $assign, $email)
    {
        $username = Session::get('username');
        $useremail = Session::get('usermail');
        $emails = array($useremail, $email);
        
        $mailData = array(
            'username' => $username,
            'ticketno' => $ticketno,
            'category' => $category,
            'priority' => $priority,
            'subject' => $subject,
            'detail' => $remark,
            'status' => $status,
            'assignedto' => $assign
        );
       
        SendtoMail::to($emails)->send(new SendMail($mailData));
    }
}