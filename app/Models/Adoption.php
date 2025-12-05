<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adoption extends Model
{
    protected $fillable = [
        'animal_id',
        'adoption_date',
        'adopter_name',
        'adopter_phone',
        'adopter_email',
        'adopter_address',
        'status',
    ];
    public $timestamps = false;
}
