<?php

namespace App\Helpers;

use Carbon\Carbon;


use Auth;

class Response
{

   public static function SUCCESS($data)
   {
     return [
        'rc' => '00',
        'desc' => 'success',
        'msg' => 'success',
        'data' => $data
      ];
   }

   public static function CREATED($data)
   {
     return [
        'rc' => '00',
        'desc' => 'success',
        'msg' => 'success insert data',
        'data' => $data
      ];
   }

   public static function ACCEPTED()
   {
     return [
        'rc' => 'XX',
        'desc' => 'unknown response',
        'msg' => 'nothing to do',
      ];
   }

   public static function UPDATED($data)
   {
     return [
        'rc' => '00',
        'desc' => 'success',
        'msg' => 'success update data',
        'data' => $data
      ];
   }

   public static function DELETED($data)
   {
     return [
        'rc' => '00',
        'desc' => 'success',
        'msg' => 'success delete data',
        'data' => $data
      ];
   }

   public static function UNAUTHORIZED()
   {
     return [
        'rc' => '01',
        'desc' => 'error',
        'msg' => 'not_authorized'
      ];
   }

   public static function PASSWORD()
   {
     return [
        'rc' => '01',
        'desc' => 'error',
        'msg' => 'wrong password'
      ];
   }

   public static function EXIST()
   {
     return [
        'rc' => '01',
        'desc' => 'error',
        'msg' => 'data already exist'
      ];
   }

   public static function NOT_FOUND()
   {
     return [
        'rc' => '01',
        'desc' => 'error',
        'msg' => 'not_found'
      ];
   }

   public static function ERROR($msg)
   {
     return [
        'rc' => '01',
        'desc' => 'error',
        'msg' => $msg,
        'data' => ''
      ];
   }

}
