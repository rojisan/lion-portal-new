<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Useraccount extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "master_data.m_user";

    protected $fillable = [
        'userid',
        'username',
        'pass',
        'departmentid',
        'plantid',
        'roleid',
        'spvid',
        'mgrid',
        'createdon',
        'usermail',
        'status_login'
    ];
}
