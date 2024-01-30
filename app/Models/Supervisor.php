<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{

    use HasFactory;

    protected $fillable = [
        'designation',
        'host_establishment',
        'campus_type',
        'working_day_start',
        'working_day_end',
        'portal_id',
        'coordinator_id'
    ];

    const OFF_CAMPUS = 'off-campus';
    const IN_CAMPUS = 'in-campus';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'portal_id', 'id');
    }
}
