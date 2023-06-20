<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;


class MailController extends Controller
{
    public static function index()
    {
        $username = Session::get('username');
        $mailData = array(
            'username' => $username,
            'ticketno' => 'Mail from ItSolutionStuff.com',
            'category' => 'This is for testing email using smtp.',
            'subject' => '',
            'detail' => '',
            'status' => '',
            'assignedto' => '',
        );

        // $arr_user = array();
        // foreach($mailData as $key => $value){
        //     array_push($arr_user, [
        //         'name' => trim($value)
        //     ]);
        // }

        Mail::to('fakhrur.rozi@lionwings.com')->send(new SendMail($mailData));

        return redirect()->route('tiket')->with("success", "successfully");

    }
}
