<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    protected $fillable = [
        'student_number',
        'coordinator_id',
        'portal_id',
    ];

}
