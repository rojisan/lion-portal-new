<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;

class AbsensipayrollController extends Controller
{
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function absenpayroll(Request $request)
    {
        return view('fitur.absensipayroll');
    }
    
}
