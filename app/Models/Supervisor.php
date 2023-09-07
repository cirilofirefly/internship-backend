<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $fillable = [
        'designation',
        'host_establishment',
        'campus_type',
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
