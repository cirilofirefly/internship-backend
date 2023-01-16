<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $fillable = [
        'designation',
        'host_establishment',
        'portal_id',
    ];

}
