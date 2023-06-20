<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiketpriority extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "master_data.m_ticket_priority";

    protected $fillable = [
        'priorid',
        'description'
    ];
}
