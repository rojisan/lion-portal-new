<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function viewUpload()
    {
        return view('fitur.tiket');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
        ]);
      
        $fileName = time().'.'.$request->file->extension();  
       
        $request->file->move(public_path('uploads'), $fileName);
     
        /*  
            Write Code Here for
            Store $fileName name in DATABASE from HERE 
        */
       
        return back()
            ->with('success','You have successfully upload file.')
            ->with('file', $fileName);
   
    }
}
