<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateTimeRecord extends Model
{
    protected $table = 'date_time_records';
    protected $fillable = [
        'date',
        'am_start_time',
        'am_end_time',
        'pm_start_time',
        'pm_end_time',
        'description',
        'overtime',
        'is_submitted',
        'is_approved',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'portal_id', 'id');
    }
}
