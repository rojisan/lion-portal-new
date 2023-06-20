<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "master_data.m_counter";

    protected $fillable = [
        'counterid',
        'prefix',
        'period',
        'description',
        'start_number',
        'end_number',
        'last_number'
    ];
}
