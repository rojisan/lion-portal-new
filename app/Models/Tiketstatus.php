<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiketstatus extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "master_data.m_ticket_status";

    protected $fillable = [
        'statusid',
        'description'
    ];
}
