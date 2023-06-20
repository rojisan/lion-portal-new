<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "helpdesk.t_ticket";

    protected $fillable = [
        'tiketno',
        'categoryid',
        'userid',
        'subject',
        'detail',
        'attachment',
        'assignedto',
        'statusid',
        'createdon',
        'approvedby_1',
        'approvedby_2',
        'approvedby_3',
        'approvedby_it',
        'priorid',
        'rejectedby',
        'reasonrejection',
        'remark',
        'closedon',
        'approvedby1_date',
        'approvedby2_date',
        'approvedby3_date',
        'approvedbyit_date',
        'createdby'
    ];
}
