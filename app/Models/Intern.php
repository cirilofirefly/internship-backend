<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    protected $fillable = [
        'student_number',
        'coordinator_id',
        'portal_id',
        'is_assigned',
        'college',
        'program',
        'section',
        'year_level'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'portal_id', 'id');
    }
}
