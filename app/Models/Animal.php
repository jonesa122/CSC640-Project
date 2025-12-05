<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $fillable = [
        'name',
        'species',
        'breed',
        'age',
        'gender',
        'arrival_date',
        'status',
    ];
    public $timestamps = false;
}
