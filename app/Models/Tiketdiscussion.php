<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiketdiscussion extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "helpdesk.t_discussion";

    protected $fillable = [
        'tiketno',
        'counterno',
        'senderid',
        'comment',
        'attachment',
        'createdon'
    ];
}
