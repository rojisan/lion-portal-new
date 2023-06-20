<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = "master_data.m_category";

    protected $fillable = [
        'categoryid',
        'description',
        'approval'
    ];
}
